<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
require_once(__DIR__.'/../vendor/palanik/corsslim/CorsSlim.php');

$corsOptions = array(
    "origin" => "*",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client",),
    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'),
    "maxAge" => 1728000,
    "allowCredentials" => True,
    "allowHeaders" => array("X-PINGOTHER", "authorization", "content-type", "x-authentication")
);
$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);