<?php

const CLIENT_ID                             = 'my-client';
const CLIENT_SECRET                         = "Jvh41Rz6OYxKVI4fESgQUUfMZVFOaWbX";
const MY_REALM                              = "RealmTest";
const PACHAGE_NAME                          = "OIDC_Keycloak_Cockpit";

//Server Side URL's Endpoints requirements
const AUTH_SERVER_IP                        = "192.168.37.53";
const AUTH_SERVER_PORT                      = "8080";
const AUTH_SERVER_URL_IP                    = AUTH_SERVER_IP.":".AUTH_SERVER_PORT;
const AUTH_SERVER_URL                       = "http://localhost:".AUTH_SERVER_PORT;

//Client Side URL's Endpoints requirements
const CLIENT_IP                             = "192.168.37.53";
const CLIENT_PORT                           = "8089";
const CLIENT_URL_IP                         = CLIENT_IP.":".CLIENT_PORT;
const CLIENT_URL                            = 'http://localhost:'.CLIENT_PORT;
const LOGOUT_URL                            = AUTH_SERVER_URL_IP."/realms/".MY_REALM."/protocol/openid-connect/logout";
const INTROSPECT_TOKEN			    = AUTH_SERVER_URL_IP."/realms/".MY_REALM."/protocol/openid-connect/token/introspect";
const REDIRECT_URI                          = CLIENT_URL."/".PACHAGE_NAME."/oauth2_keycloak.php";
const RS256_ENCRYPTION_ALGORITHM            = "RS256";
const CODE_TOKEN_TYPE                       = "authorization_code";
const REFRESH_TOKEN_TYPE                    = "refresh_token";
const JWKS_URL                              = AUTH_SERVER_URL_IP."/realms/".MY_REALM."/protocol/openid-connect/certs";
const TOKEN_DESTINATION                     = AUTH_SERVER_URL_IP."/realms/".MY_REALM."/protocol/openid-connect/token";
const KEYCLOAK_VERSION                      = "20.0.1";
