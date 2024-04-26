<?php

use GuzzleHttp\Client;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use \Firebase\JWT\JWT;

session_start(); //for auth
require $_SERVER['DOCUMENT_ROOT'] . '/lib/vendor/autoload.php';
include_once(__DIR__ . "/constant_variables_oauthe2.php");

const SETTINGS_KEYCLOAK = [
    'authServerUrl'         => AUTH_SERVER_URL,
    'realm'                 => MY_REALM,
    'clientId'              => CLIENT_ID,
    'clientSecret'          => CLIENT_SECRET,
    'redirectUri'           => REDIRECT_URI,
    'encryptionAlgorithm'   => RS256_ENCRYPTION_ALGORITHM,            // optional
    'version'               => KEYCLOAK_VERSION                       // optional
    //'encryptionKeyPath'   => '../key.pem',                          // optional
    //'encryptionKey'       => 'contents_of_key_or_certificate'       // optional
];

const ERROR_DESCRIPTION = [
	"state"		  	   	=>"Invalid state, make sure HTTP sessions are enabled.",
	"reuse_code"			=>"you can't make more than request with the same authorization_code.",
	"code"		  	   	=>"Failed to get authorization_code from keycloak ldp.",
	"tokens"		   	=>"faild to get tokens from keycloak ldp.",
	"validate_extract_id_token"	=>"failed to validate and extract ID_TOKEN !!",
	"public_key"		   	=>"failed to request public_key from JWKS !!",
	"login_api"			=>"failed to Request loginApi"
];

if (!defined('ERROR_AUTHENTICATION_PAGE')) {
    define('ERROR_AUTHENTICATION_PAGE', $_SERVER['DOCUMENT_ROOT'] . '/OIDC_Keycloak_Cockpit/pages/400.php');
}
if (!defined('SUCCESS_AUTHENTICATION_PAGE')) {
    define('SUCCESS_AUTHENTICATION_PAGE', $_SERVER['DOCUMENT_ROOT'] . '/OIDC_Keycloak_Cockpit/pages/success_authentication.php');
}

$provider_instance = new Keycloak(SETTINGS_KEYCLOAK);
// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {
    // If we don't have an authorization code, then get one
    $authUrl = $provider_instance->getAuthorizationUrl();
    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider_instance->getState();
    // Redirect the user to the authorization URL.
    header('Location: ' . $authUrl);
    exit;

} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !==  $_SESSION['oauth2state'])) { // Check given state against previously stored one to mitigate CSRF attack

    if (isset($_SESSION['oauth2state'])) unset($_SESSION['oauth2state']);

    redirectWithError(ERROR_DESCRIPTION['code']);
} else { // Try to get an access token (using the authorization_code grant)

   $authorization_code = $_GET['code'];

   if($authorization_code){
        try{
	   $tokens = requestTokensFromKeycloakAuthServer($authorization_code);
	   if(empty($tokens)) redirectWithError(ERROR_DESCRIPTION['tokens']);
        }catch(Exception $e){ //when you try to make more than request with the same authorization_code !
	   redirectWithError(ERROR_DESCRIPTION['reuse_code']);
	}

	$_SESSION['refresh_token'] = $tokens['refresh_token']; //use in logout SSO
	$_SESSION['access_token'] = $tokens['access_token'];  //use in introspect endpoint
        if(isset($tokens['id_token']))
	   $id_token = $tokens['id_token'];
        else
	   redirectWithError(ERROR_DESCRIPTION['tokens']);

	try{
          $public_key=getPublicKeyFromJWKS();
	  if(!$public_key) redirectWithError(ERROR_DESCRIPTION['public_key']);
	}catch(Exception $e){
	    redirectWithError(ERROR_DESCRIPTION['public_key']);
	}

	try{
           $extract_token_id_data = validationAndExtractTokenID($id_token, $public_key);
	}catch(Exception $e){
	    redirectWithError(ERROR_DESCRIPTION['validate_extract_id_token']);
	}

         //Set the variable value in a session variable
         $_SESSION['u_name'] = $extract_token_id_data['upn']; //session start in the main bootstrap
         $_SESSION['u_email'] = $extract_token_id_data['email'];

	try{
           echo loginCockpit($extract_token_id_data['upn'], $extract_token_id_data['email']);
	}catch(Exception $e){
           redirectWithError(ERROR_DESCRIPTION['login_api']);
	}

   }else{ //failed to get authorization_Code from keycloak
	redirectWithError(ERROR_DESCRIPTION['code']);
   }
}

function getSettingsToken($authorization_code, $type=CODE_TOKEN_TYPE){
        return [
            'grant_type' => $type,
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'redirect_uri' => REDIRECT_URI,
            'code' => $authorization_code
        ];
}

function requestTokensFromKeycloakAuthServer($authorization_code, $token_type=CODE_TOKEN_TYPE){
       //Send the token request to Keycloak
        $token_settings = getSettingsToken($authorization_code, $token_type);

        $client = new Client();
        $result = $client->post(TOKEN_DESTINATION, [
                'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' =>$token_settings
        ]);

        if($result->getStatusCode() == 200){
            $response_body = $result->getBody()->getContents();
            $arr_response_body = json_decode($response_body, true);
            return $arr_response_body;
        }
}

function getPublicKeyFromJWKS(){
        // Make a request to retrieve the JWKS
        $client = new Client();
        $response = $client->get(JWKS_URL);
        $public_key = '';

        // Extract the public key from the JWKS
        if($response->getStatusCode() == 200){
            $jwks = json_decode($response->getBody()->getContents(), true);
            if (isset($jwks['keys'][0]['x5c'][0]))
                $public_key = "-----BEGIN CERTIFICATE-----\n".
                               $jwks['keys'][0]['x5c'][0].
                              "\n-----END CERTIFICATE-----";
        }

        return $public_key;
}

function validationAndExtractTokenID($id_token, $public_key, $encryption_algorithm=RS256_ENCRYPTION_ALGORITHM){
        $decoded_token = JWT::decode($id_token, $public_key, [$encryption_algorithm]); //return object std
	return json_decode(json_encode($decoded_token),true);
}

function loginCockpit($u_name, $u_email){
        $cockpit_entry_point_api = CLIENT_URL_IP."/api/cockpit/loginApi";
        $client = new Client();
        $result = $client->post($cockpit_entry_point_api, [
           'json'=>['u_name'=>$u_name, 'u_email'=>$u_email]
        ]);

        if($result->getStatusCode() == 200){
            $response_body = $result->getBody()->getContents();
            return $response_body;
        }
}

function redirectWithError($error_description, $page=ERROR_AUTHENTICATION_PAGE){
     $_GET['error_description'] = $error_description;
     include_once($page);
}

function redirectWithSuccess($page=SUCCESS_AUTHENTICATION_PAGE){
      include_once($page);
}
