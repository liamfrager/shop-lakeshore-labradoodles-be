<?php
require 'vendor/autoload.php';
include_once __DIR__ . '/printful.php';
include_once __DIR__ . '/../config/loadEnv.php';

class StripeService {
    public function __construct() {
        \Stripe\Stripe::setApiKey(getenv("STRIPE_API_KEY"));
    }

    public function createCheckoutSession($cart) {
        try {
            $metadata = array_reduce(
                array_keys($cart['items']),
                function($acc, $id) use ($cart) {
                    $acc[(int)$id] = $cart['items'][$id]['quantity'];
                    return $acc;
                },
                []  // Starting with an empty array
            );

            $checkoutSessionParams =[
                'line_items' => self::getLineItems($cart),
                'mode' => 'payment',
                'shipping_address_collection' => ['allowed_countries'=>['US']],
                'success_url' => getenv("FE_DOMAIN") . "/success",
                'cancel_url' => getenv("FE_DOMAIN") . "/cart",
                'metadata' => $metadata,
            ];
            return  \Stripe\Checkout\Session::create($checkoutSessionParams);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getLineItems($cart) {
        $lineItems = [];
        
        foreach ($cart['items'] as $id => $item) {
            $variant = PrintfulService::getVariant((int)$id);
            $lineItem = [
                'price_data' => StripeService::getPriceData($variant),
                'quantity' => $item['quantity']
            ];
            $lineItems[] = $lineItem;
        }
        
        return $lineItems;
    }
    
    public static function getPriceData($variant) {
        return [
            'currency' => strtolower($variant['currency']),
            'unit_amount' => (int)(str_replace('.', '', $variant['retail_price'])),
            'product_data' => [
                'name' => $variant['name'],
                'description' => $variant['name'],  // Placeholder description
                'images' => array_reverse(array_map(function($file) {
                    return $file['thumbnail_url'];
                }, $variant['files']))
            ]
        ];
    }
}
?>