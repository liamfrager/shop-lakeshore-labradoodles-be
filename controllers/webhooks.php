<?php
require_once 'vendor/autoload.php';  // Make sure Stripe SDK is installed via Composer
include_once __DIR__ . '/../services/email.php';
include_once __DIR__ . '/../services/orders.php';
include_once __DIR__ . '/../config/loadEnv.php';

error_log("\nWebhook received: " . json_encode($_POST));
// Set your secret key (from Stripe dashboard)
$stripe = \Stripe\Stripe::setApiKey(getenv('STRIPE_API_KEY'));

// Your Stripe webhook secret (from your Stripe Dashboard)
$webhookSecret = getenv('STRIPE_WEBHOOK_SIGNING_SECRET');

$payload = @file_get_contents("php://input");
if (empty($payload)) {
    error_log('Received empty payload');
    http_response_code(400);
    exit('Empty payload');
}

if (isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
    $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
} else {
    // Handle missing Stripe signature header
    error_log("Stripe signature header missing");
    http_response_code(400);
    exit("Missing Stripe signature header");
}

try {
    // Verify the webhook event
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sigHeader, $webhookSecret
    );

    // Log the event for debugging
    error_log("WEBHOOK EVENT VALIDATED => " . $event->type);

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.succeeded':
            // Payment succeeded
            $paymentIntent = $event->data->object; // Contains a Stripe\PaymentIntent
            $checkoutSession = $sessions = \Stripe\Checkout\Session::all([
                'payment_intent' => $paymentIntent->id,
                'expand' => ['data.line_items'],
            ])['data'][0];
            $orderResponse = OrderService::placeOrder($checkoutSession);
            error_log(('ORDER RESPONSE: ' . print_r($orderResponse, true)));
            $order = $orderResponse['result'];
            // If the order succeeded
            if ($orderResponse['code'] == 200) {
                EmailService::sendOrderConfirmationEmail($order);
                error_log("ORDER SUCCESS EMAIL SENT => " . $order['recipient']['email']);
                http_response_code(200);
                exit;
            } else {
                // If order failed
                EmailService::sendOrderFailedEmail($orderResponse, $paymentIntent);
                error_log("ORDER FAILED EMAIL SENT => liam.frager@gmail.com");
                http_response_code(400);
                exit;
            }
            break;

        default:
            // Handle other event types
            error_log("Unhandled event type " . $event->type);
            http_response_code(200);
            exit;
            break;
    }

} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    error_log("Webhook signature verification failed: " . $e->getMessage());
    http_response_code(400);
    exit;
} catch (Exception $e) {
    // Something went wrong
    error_log("Webhook error: " . $e->getMessage());
    http_response_code(500);
    exit;
}

function placeOrder($checkoutSession) {
    // Implement the logic to place an order on Printful (or your system)
    // Returning a mock response for the sake of example
    return [
        'code' => 200,  // Mock successful order response code
        'result' => [
            'recipient' => [
                'email' => 'customer@example.com',
            ]
        ]
    ];
}
?>