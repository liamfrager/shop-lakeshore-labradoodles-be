<?php
require_once 'vendor/autoload.php';  // Assuming you have installed Stripe SDK via Composer
require_once 'printful.php';  // Assuming PrintfulService class is available

class OrderService {

    // Takes a Stripe Checkout Session object and places an order on Printful
    public static function placeOrder($checkoutSession) {
        // Create the order array with recipient information
        $order = [
            'recipient' => [
                'name' => $checkoutSession->shipping_details->name ?? 'CUSTOMER NAME',
                'address1' => $checkoutSession->shipping_details->address->line1 ?? 'ADDRESS',
                'address2' => $checkoutSession->shipping_details->address->line2 ?? '',
                'city' => $checkoutSession->shipping_details->address->city ?? 'CITY',
                'state_code' => $checkoutSession->shipping_details->address->state ?? 'STATE',
                'country_code' => $checkoutSession->shipping_details->address->country ?? 'COUNTRY',
                'zip' => $checkoutSession->shipping_details->address->postal_code ?? 'ZIP CODE',
                'phone' => $checkoutSession->customer_details->phone ?? 'PHONE NUMBER',
                'email' => $checkoutSession->customer_details->email ?? 'EMAIL',
            ],
            'items' => [],
            'packing_slip' => [
                'email' => 'lakeshorelabradoodles@gmail.com',
                'phone' => '+1(860)478-0267',
                'message' => 'Thank you for your purchase!',
                'logo_url' => 'https://shop.lakeshorelabradoodles.com/static/logo.png',
                'store_name' => 'Lakeshore Labradoodles',
            ]
        ];

        // Convert metadata to items
        if (isset($checkoutSession->metadata)) {
            foreach ($checkoutSession->metadata as $id => $quantity) {
                $order['items'][] = [
                    'sync_variant_id' => (int)$id,
                    'quantity' => (int)$quantity
                ];
            }
        }
        error_log('PLACING ORDER: ' . print_r($order, true));

        // Call the PrintfulService to place the order
        return PrintfulService::placeOrder($order);
    }
}