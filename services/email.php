<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

include_once __DIR__ . '/../config/loadEnv.php';
loadEnv(__DIR__ . '/../.env');

class EmailService {

    private static $emailHostUser;
    private static $emailHostPassword;

    public static function init() {
        self::$emailHostUser = getenv('EMAIL_HOST_USER');
        self::$emailHostPassword = getenv('EMAIL_HOST_PASSWORD');

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
                'email' => 'liam.frager@gmail.com',
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
        // self::sendOrderConfirmationEmail($order);
    }

    private static function getEmailBase() {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = self::$emailHostUser;
        $mail->Password = self::$emailHostPassword; // Use App Password, not your actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->isHTML(true);

        $mail->setFrom('lakeshorelabradoodles@gmail.com', 'Lakeshore Labradoodles');

        return $mail;
    }

    private static function sendMailFromTemplate($to, $subject, $body) {
        try {
            $mail = self::getEmailBase();
            if (is_array($to)) {
                foreach ($to as $t) {
                    $mail->addAddress($t);
                }
            } else {
                $mail->addAddress($to);
            }
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            echo 'Email sent successfully.';
        } catch (Exception $e) {
            echo "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    }

    public static function sendOrderConfirmationEmail($order) {
        error_log("Sending order confirmation to " . $order['recipient']['email'] . "...");

        ob_start();
        require_once __DIR__ . '/../email-templates/order-confirmation.php';
        $template = ob_get_clean();

        self::sendMailFromTemplate($order['recipient']['email'], 'Order Confirmation', $template);
    }

    public static function sendOrderFailedEmail($orderResponse, $paymentIntent) {
        error_log("Sending order failed email to liam.frager@gmail.com...");

        ob_start();
        require_once __DIR__ . '/../email-templates/order-failure.php';
        $template = ob_get_clean();

        self::sendMailFromTemplate('liam.frager@gmail.com', 'Issue with order!', $template);
    }
}

EmailService::init();
?>