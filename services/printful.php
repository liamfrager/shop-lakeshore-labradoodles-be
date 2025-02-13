<?php
include_once __DIR__ . '/../config/loadEnv.php';
loadEnv(__DIR__ . '/../.env');

class PrintfulService {
    private static $authToken;
    private static $apiEndpoint;
    private static $apiHeaders;

    public static function init() {
        self::$authToken = getenv('PRINTFUL_AUTH_TOKEN');
        self::$apiEndpoint = 'https://api.printful.com';
        self::$apiHeaders = [
            'Authorization: Bearer ' . self::$authToken
        ];
    }

    // Returns all sync products from the Printful shop.
    public static function getAllProducts() {
        $url = self::$apiEndpoint . '/sync/products';
        $response = self::makeRequest($url, 'GET', ['status' => 'all']);
        return $response['result'];
    }

    // Takes a Printful sync product ID as an input and returns details on the product.
    public static function getProduct($id) {
        $url = self::$apiEndpoint . '/sync/products/' . $id;
        $response = self::makeRequest($url, 'GET', ['limit' => 100]);
        return $response['result'];
    }

    // Takes a Printful sync product ID and returns details on all its variants
    public static function getVariantIDs($id) {
        $product = self::getProduct($id);
        $variantIDs = array_map(function ($variant) {
            return $variant['id'];
        }, $product['sync_variants']);
        return $variantIDs;
    }

    // Takes a Printful sync variant ID and returns details on that variant.
    public static function getVariant($id) {
        $url = self::$apiEndpoint . '/sync/variant/' . $id;
        $response = self::makeRequest($url, 'GET');
        return $response['result']['sync_variant'];
    }

    // Takes a Printful product variant ID and returns the color code associated with that variant.
    public static function getColorCode($id) {
        $url = self::$apiEndpoint . '/products/variant/' . $id;
        $response = self::makeRequest($url, 'GET');
        return $response['result']['variant']['color_code'];
    }

    // Takes Printful order data and places an order. Returns an API response from Printful.
    public static function placeOrder($order) {
        $url = self::$apiEndpoint . '/orders';
        $params = ['confirm' => getenv('AUTO_FULFILL_PRINTFUL_ORDERS')];
        $response = self::makeRequest($url, 'POST', $order, $params);
        return $response;
    }

    // Makes an HTTP request using cURL.
    private static function makeRequest($url, $method, $data = null, $params = []) {
        $curl = curl_init();

        // Set the cURL options
        $options = [
            CURLOPT_URL => $url . '?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => self::$apiHeaders,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        
        // Execute the cURL request
        $response = curl_exec($curl);

        // Check if there is an error
        if (curl_errno($curl)) {
            throw new Exception('Curl error: ' . curl_error($curl));
        }

        curl_close($curl);

        // Decode and return the JSON response
        return json_decode($response, true);
    }
}

PrintfulService::init();
?>