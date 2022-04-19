<?php
require realpath(__DIR__.'/../../vendor/autoload.php');

use Firebase\JWT\JWT;

$key = "JSJSR%jsjsfjSFJASfghasFFJXdr3";
$payload = array(
    "iss" => "http://DressApi-rest", // definisce l’emittente del token. Può essere un nome di dominio e può essere utilizzato per eliminare i token da altre applicazioni.
    "aud" => "http://DressApi-rest", // definisce il pubblico del token (un dominio)
    "iat" => 1356999524,              // the timestamp of token issuing.
    "nbf" => 1357000000,
    "alg" => "RS256", // Algoritmo usato per la cifratura del token
    "typ" => "JWT",   // Tipo di Token 
    // "exp": 3600 fornisce un tempo di scadenza al token. Una volta trascorso questo tempo di scadenza, il token
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
$jwt = JWT::encode($payload, $key);
$decoded = JWT::decode($jwt, $key, array('HS256'));

print_r($decoded);

/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

$decoded_array = (array) $decoded;

/**
 * You can add a leeway to account for when there is a clock skew times between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 */
JWT::$leeway = 60; // $leeway in seconds
$decoded = JWT::decode($jwt, $key, array('HS256'));
