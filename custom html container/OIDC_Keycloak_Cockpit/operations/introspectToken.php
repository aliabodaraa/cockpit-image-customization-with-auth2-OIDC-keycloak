<?php

function introspectToken(){
    include_once(__DIR__ . "/../constant_variables_oauthe2.php");
    // Create a Guzzle client with the custom handler stack
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, INTROSPECT_TOKEN);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
      'token' => $_SESSION['access_token'],
      'client_id' => 'my-client',
      'client_secret' => 'Jvh41Rz6OYxKVI4fESgQUUfMZVFOaWbX',
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      'Host: localhost:8080'
    ]);
    $status=false;
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status_code == 200) {
       $is_active = json_decode($response, true)['active'];
       $status = $is_active;
       //var_dump($_SESSION['access_token'],json_decode($response, true),"----",$status, $is_active);
    }
   return $status;
}
