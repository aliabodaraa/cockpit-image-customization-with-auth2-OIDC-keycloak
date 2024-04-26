<?php
session_start();
use GuzzleHttp\Client;
require '../../../../lib/vendor/autoload.php'; // Assuming you have Composer autoloading set up
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use Jumbojett\OpenIDConnectClient;
use \Firebase\JWT\JWT;

$client_id='my-client';
$client_secret="Jvh41Rz6OYxKVI4fESgQUUfMZVFOaWbX";
$auth_server_url="http://localhost:8080/realms/RealmTest";
$redirect_uri="http://localhost:8089/auth/check";
$my_realm="RealmTest";
$encryption_algorithm="RS256";
$settings_keycloak=[
    'authServerUrl'         =>$auth_server_url,
    'realm'                 => $my_realm,
    'clientId'              => $client_id,
    'clientSecret'          => $client_secret,
    'redirectUri'           => $redirect_uri,
    //'encryptionAlgorithm'   => 'RS256',                             // optional
    //'encryptionKeyPath'     => '../key.pem',                        // optional
    //'encryptionKey'         => 'contents_of_key_or_certificate',     // optional
    'version'               => '20.0.1',                            // optional
];
$provider = new Keycloak($settings_keycloak);
$authorization_code=$_GET['code'];
// If we don't have an authorization code then get one
if (!isset($authorization_code)) {
    // If we don't have an authorization code, then get one
    $authUrl = $provider->getAuthorizationUrl();
    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();
    // Redirect the user to the authorization URL.
    header('Location: ' . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
    if (isset($_SESSION['oauth2state']))
        unset($_SESSION['oauth2state']);
    exit('Invalid state, make sure HTTP sessions are enabled.');
} else {
   // Try to get an access token (using the authorization coe grant)
   try{
	$authorization_code = $_GET['code'];
        $token_settings = [
                 'grant_type' => 'authorization_code',
                 'client_id' => $client_id,
                 'client_secret' => $client_secret,
                 'redirect_uri' => $redirect_uri,
                 'code' => $authorization_code
        ];
	$tokens=requestTokensFromAuthServer($token_settings);
	//var_dump($tokens);
	if (isset($tokens['id_token'])) {
    	  $id_token = $tokens['id_token'];
	}
	$public_key=getPublicKeyFromJWKS();
	//echo $public_key;
	list($is_valid_id_token,$extract_token_id_data)=tokenIdValidationAndExtract($id_token,$public_key,$encryption_algorithm);
	if($is_valid_id_token)
	    var_dump($extract_token_id_data);
	else
	    var_dump("error");
   } catch (Exception $e) {
	//use refresh token to request a new token
        exit('Failed to get access token: '.$e->getMessage());
    }
    // Optional: Now you have a token you can look up a users profile data
    try {
        // We got an access token, let's now get the user's details
        //$user = $provider->getResourceOwner($token);
        // Use these details to create a new profile
        //printf('Hello %s!', $user->getName());

    } catch (Exception $e) {
        exit('Failed to get resource owner: '.$e->getMessage());
    }
}

function requestTokensFromAuthServer($token_settings){
        //Send the token request to Keycloak
        $token_destination="192.168.37.53:8080/realms/RealmTest/protocol/openid-connect/token";
        $client = new Client();
        $result = $client->post($token_destination, [
                'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                 'form_params' =>$token_settings
        ]);
        //var_dump($result->getBody()->getContents(),$authorizationCode);
        $responseBody=$result->getBody()->getContents();
        $responseData = json_decode($responseBody, true);
        return $responseData;
}
function getPublicKeyFromJWKS(){
        // Retrieve the JWKS from the Keycloak server
        $jwks_url = '192.168.37.53:8080/realms/RealmTest/protocol/openid-connect/certs';
        // Make a request to retrieve the JWKS
        $client = new Client();
        $response = $client->get($jwks_url);
        $jwks = json_decode($response->getBody()->getContents(), true);
        // Extract the public key from the JWKS
        $public_key = '';
        if (isset($jwks['keys'][0]['x5c'][0])) {
            $public_key = "-----BEGIN CERTIFICATE-----\n" . $jwks['keys'][0]['x5c'][0] . "\n-----END CERTIFICATE-----";
        }
        return $public_key;
};
function tokenIdValidationAndExtract($id_token,$public_key,$encryption_algorithm){
        //verify id_token
        try {
           $decoded_token = JWT::decode($id_token, $public_key, [$encryption_algorithm]);//return object std
           // Token is valid
          // echo 'Token is valid!</br>';
           // You can access the claims in the decoded token like this:
          // echo 'User ID: ' . $decoded_token->sub."</br>";
          // echo 'Username: ' . $decoded_token->username;
          // ...
         // var_dump($decoded_token);
         // return [true,[$decoded_token->given_name,$decoded_token->family_name,$decoded_token->email]];
        return [true,json_decode(json_encode($decoded_token),true)];
	} catch (Exception $e) {
          // Failed to verify the token
	  return [false,[]];
          //echo 'Token verification failed: ' . $e->getMessage();
        }
}
