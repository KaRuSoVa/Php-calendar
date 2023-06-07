<?php
require_once('../../vendor/autoload.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_email($emailAddress, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        $mail->isSMTP();
        $mail->Host       = 'ssl://smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gokhanbasturk12@gmail.com';
        $mail->Password   = 'YOUR PASSWORD';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;

        $mail->setFrom('gokhanbasturk12@gmail.com', 'Feurst Calendar');
        $mail->addAddress($emailAddress);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return 'The mail has been sent succesfully.';
    } catch (Exception $e) {
        return 'The Mail has been not sent: ' . $mail->ErrorInfo;
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$emails = $data['emails'];
$subject = $data['subject'];
$body = $data['body'];

foreach ($emails as $email) {
    $result = send_email($email, $subject, $body);
    if (strpos($result, 'The mail has been sent succesfully') === false) {
        // Print the error message to console
        echo 'E-posta gönderilemedi: ' . $result . PHP_EOL;
    }
}
?>
