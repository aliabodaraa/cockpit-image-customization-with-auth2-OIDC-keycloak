<?php
use GuzzleHttp\Client;
include_once(__DIR__ . "/../constant_variables_oauthe2.php");

function logoutKeycloak(){
       //logout user session from SSO Keycloak
        $client = new Client();
        try{
            $result = $client->post(/*"http://127.0.0.1:8080/realms/RealmTest/protocol/openid-connect/logout"*/LOGOUT_URL, [
/*                'force_ip_resolve' => 'v4',*/
		'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' =>[
                    'refresh_token' => $_SESSION['refresh_token'],
                    'client_id' => CLIENT_ID,
                    'client_secret' => CLIENT_SECRET,
                ]
            ]);
          }catch (RequestException $e) {
             exit('Logout SSO Exception: '.$e->getMessage());
          };
}
