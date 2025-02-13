<!-- Requires a Printful order, Stripe checkout session, and Stripe payment intent to be passed in as context. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Merriweather:wght@400;700&display=swap">
    <title>Order Failed</title>
    <style>
        * { color: #4f3528; font-family: 'Merriweather', serif; }
        body { background-color: #f4ede5; }
        h1, h3 > * { font-family: 'Lato', sans-serif;}
        hr { border: 1px solid #4f3528; margin: 2em 0; }
        a { color: #248cc2; }
    </style>
<body>
    <h1>An order failed!</h1>
    <p>Reach out to the customer ASAP to let them know what happened.</p>

    <hr>
    <h3>Printful Order Response:</h3>
    <p><?php echo $orderResponse['result']?></p>
    <br>
    <h3>Stripe Payment Intent:</h3> 
    <a href="<?php echo 'https://dashboard.stripe.com/test/payments/' . $paymentIntent->id ?>">
        <?php echo $paymentIntent->id ?>
    </a>

    <hr>
    <img src="https://shop.lakeshorelabradoodles.com/static/logo.png" alt="Lakeshore Labradoodles Logo" height="100">
</body>
</html>
