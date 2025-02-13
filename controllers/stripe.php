<?php
include_once __DIR__ . '/../services/stripe.php';
header("Content-Type: application/json");

$stripe = new StripeService();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $cart = json_decode(file_get_contents("php://input"), true)['body'];
    echo json_encode($stripe->createCheckoutSession($cart));
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>