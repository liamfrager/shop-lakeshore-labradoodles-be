<?php
include_once __DIR__ . '/../services/printful.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (preg_match('#^/api/products/(\d+)$#', $requestUri, $matches)) {
        $productId = $matches[1];
        echo json_encode(PrintfulService::getProduct($productId));
    }
    elseif (preg_match('#^/api/variants/(\d+)$#', $requestUri, $matches)) {
        $variantId = $matches[1];
        echo json_encode(PrintfulService::getVariant($variantId));
    }
    elseif (preg_match('#^/api/products/(\d+)/color$#', $requestUri, $matches)) {
        $colorId = $matches[1];
        echo json_encode(PrintfulService::getColorCode($colorId));
    }
    else {
        echo json_encode(PrintfulService::getAllProducts());
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>