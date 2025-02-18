<?php
include_once __DIR__ . '/services/email.php';
include_once __DIR__ . '/config/loadEnv.php';

header("Access-Control-Allow-Origin: " . getenv("FE_DOMAIN"));
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$requestUri = $_SERVER['REQUEST_URI'];

if (preg_match('#^/api/(products|variants)(/.*)?$#', $requestUri)) {
    include 'controllers/printful.php';
} 
elseif (preg_match('#^/api/orders(/.*)?$#', $requestUri)) {
    include 'controllers/orders.php';
} 
elseif (preg_match('#^/api/stripe(/.*)?$#', $requestUri)) {
    include 'controllers/stripe.php';
} 
elseif (preg_match('#^/webhooks(/.*)?$#', $requestUri)) {
    include 'controllers/webhooks.php';
} 
else {
   include 'controllers/test.php';
}
?>