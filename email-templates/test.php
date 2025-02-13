<?php
$order = [
    'id' => '12345',
    'recipient' => [
        'name' => 'Brittany Frager',
        'address1' => '6 Percy Drive',
        'address2' => '',
        'city' => 'Wolfeboro',
        'state_code' => 'NH',
        'zip' => '03894',
        'country_name' => 'USA',
        'email' => 'brittanyjunefrager@gmail.com',
    ],
    'items' => [
        [
            'name' => 'Doodle Mom Unisex T-Shirt',
            'retail_price' => 25.00,
            'quantity' => 2,
            'files' => [['thumbnail_url' => 'https://files.cdn.printful.com/files/5ec/5ecf9bf622593901044cd46571657199_preview.png']],
        ],
        [
            'name' => 'Life is Better with a Lakeshore Labradoodle Unisex Sweatshirt',
            'retail_price' => 35.00,
            'quantity' => 1,
            'files' => [['thumbnail_url' => 'https://files.cdn.printful.com/files/132/1328ff6df99a86b2ee986c6991554b13_preview.png']],
        ],
    ],
];
ob_start();
require_once __DIR__ . '/../email-templates/order-confirmation.php';
$template = ob_get_clean();
echo $template;
?>