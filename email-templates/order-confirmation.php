<!-- Requires a Printful order to be passed in as context. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Merriweather:wght@400;700&display=swap">
    <title>Order Confirmation</title>
    <style>
        * { color: #4f3528; font-family: 'Merriweather', serif; }
        body { background-color: #f4ede5; }
        h1, h2, tr > * { font-family: 'Lato', sans-serif;}
        hr { border: 1px solid #4f3528; margin: 2em 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; color: #4f3528; }
        table, th, td { border: 1px solid #f4ede5; }
        th, td { padding: 8px; text-align: left; border-radius: 5px; }
        th { background-color: #9b572a; color: #f4ede5; }
        td { background-color: #fff;}
        tr:not(:last-of-type) > td:first-of-type { text-align: center;}
        .bold { font-weight: bold; color: #dea36d}
        .sans-serif { font-family: 'Lato', sans-serif; }
        .right-align { text-align: right; }
    </style>
</head>
<body>
    <h1>Thank you for your order<?php echo !empty($order['recipient']['name']) ? ', ' . explode(' ', $order['recipient']['name'])[0] : ''; ?>!</h1>
    <p>Your order ID is <span class="bold sans-serif"><?php echo $order['id']; ?></span>.</p>
    <br>
    <p>We appreciate your business and hope you enjoy your purchase.</p>
    <p>If you have any questions, feel free to reply to this email.</p>
    <br>
    <p>Best regards,</p>
    <p>Rahna</p>
    <hr>
    
    <!-- LINE ITEMS -->
    <?php if (!empty($order['items'])): ?>
        <h2>Order Details:</h2>
        <table>
            <thead>
                <tr>
                    <th colspan="2">Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><img src="<?php echo end($item['files'])['thumbnail_url']; ?>" alt="<?php echo $item['name']; ?>" height="30"></td>
                        <td><?php echo $item['name']; ?></td>
                        <td>$<?php echo number_format($item['retail_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['retail_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="right-align bold" colspan="4">Order Total</td>
                    <td class="bold">
                        $<?php
                        $subtotal = array_sum(array_map(fn($item) => $item['retail_price'] * $item['quantity'], $order['items']));
                        echo number_format($subtotal, 2);
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- SHIPPING -->
    <?php if (!empty($order['recipient'])): ?>
        <h2>Shipping Address:</h2>
        <p>
            <?php echo $order['recipient']['name']; ?><br>
            <?php echo $order['recipient']['address1']; ?><br>
            <?php echo !empty($order['recipient']['address2']) ? $order['recipient']['address2'] . '<br>' : ''; ?>
            <?php echo $order['recipient']['city'] . ', ' . $order['recipient']['state_code'] . ' ' . $order['recipient']['zip']; ?><br>
            <?php echo $order['recipient']['country_name']; ?><br>
        </p>
    <?php endif; ?>

    <!-- LOGO IMAGE -->
    <hr>
    <img src="https://shop.lakeshorelabradoodles.com/static/images/logo.png" alt="Lakeshore Labradoodles Logo" height="100">
</body>
</html>