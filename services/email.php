<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

include_once __DIR__ . '/../config/loadEnv.php';

class EmailService {

    private static $emailHostUser;
    private static $emailHostPassword;

    public static function init() {
        self::$emailHostUser = getenv('EMAIL_HOST_USER');
        self::$emailHostPassword = getenv('EMAIL_HOST_PASSWORD');
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