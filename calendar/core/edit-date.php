<?php
// Connexion à la base de données
require_once('../utils/auth.php');
require_once('../../vendor/autoload.php');
require_once('../../first.php');

$editby =$_SESSION["username"];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_email($emailAddresses, $subject, $body) {
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
        $mail->addAddress($emailAddresses);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return 'E-posta gönderildi';
    } catch (Exception $e) {
        return 'E-posta gönderilemedi. Hata: ' . $mail->ErrorInfo;
    }
}

if (isset($_POST['Event'][0]) && isset($_POST['Event'][1]) && isset($_POST['Event'][2])){

    $id = $_POST['Event'][0];
    $start = $_POST['Event'][1];
    $end = $_POST['Event'][2];

    $sql = "SELECT title, writtenby, start, end, email_addresses,responsible_persons FROM events WHERE id = $id";
    $prepareQuery = $auth->prepare($sql);

    if ($prepareQuery == false) {
        print_r($auth->errorInfo());
        die ('Error preparing the query.');
    }

    $executeQuery = $prepareQuery->execute();

    if ($executeQuery == false) {
        print_r($prepareQuery->errorInfo());
        die ('Error executing the query.');
    }

    $eventData = $prepareQuery->fetch(PDO::FETCH_ASSOC);
    $eventName = $eventData['title'];
    $oldStart = $eventData['start'];
    $oldEnd = $eventData['end'];
    $emailAddresses = explode(",", $eventData['email_addresses']);
	$writtenby = $eventData['writtenby'];
    $responsible_persons = $eventData['responsible_persons'];

    


    $sql = "UPDATE events SET start = '$start', end = '$end', updated_by='$editby' WHERE id = $id";
    $prepareQuery = $auth->prepare($sql);

    if ($prepareQuery == false) {
        print_r($auth->errorInfo());
        die ('Error preparing the query.');
    }

    $executeQuery = $prepareQuery->execute();

    if ($executeQuery == false) {
        print_r($prepareQuery->errorInfo());
        die ('Error executing the query.');
    } else {
		if ($editby === $writtenby && empty($responsible_persons)) {
            exit;
        }
		$subject = "L'événement a été mis à jour";
		$mailBody = "
            <html>
            <head>
                <style>
                    .container {
                        font-family: Arial, sans-serif;
                        padding: 10px;
                        border: 1px solid #ccc;
                        background-color: #f2f2f2;
                    }
                    h2 {
                        margin-bottom: 0;
                        color: #1c2331;
                    }
                    p {
                        margin-top: 5px;
                        color: #1c2331;
                    }
                    .event-name {
                        font-weight: bold;
                        color: #008080;
                    }
					.write-name {
                        font-weight: bold;
                        color: #00539b;
                    }
                    .event-dates {
                        font-style: italic;
                        color: #1c2331;
                    }
                </style>
            </head>
            <body>
                <div class=\"container\">
                    <h2>Événement mis à jour avec succès</h2>
                    <p>L'événement suivant a été modifié :</p>
                    <p class=\"event-name\">$eventName</p>
					<p class=\"write-name\">Modifié par $editby </p>
                    <p class=\"event-dates\">Dates : $oldStart - $oldEnd → $start - $end</p>
                </div>
            </body>
            </html>
        ";
      // E-posta adreslerini diziye ekleyin
$emailAddresses = explode(",", $eventData['email_addresses']);
$logData = '[' . date('Y-m-d H:i:s') . '] ' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR']) . ' ' . $_SESSION['username'] . ' updated event ' . $eventName . ' (' . $oldStart . ' - ' . $oldEnd . ' => ' . $start . ' - ' . $end . ')' . PHP_EOL;
$logFile = 'edit-date.log';
file_put_contents($logFile, $logData, FILE_APPEND);
// Benzersiz e-posta adreslerini dizi öğeleri filtreleyerek alın
$uniqueEmailAddresses = array_unique($emailAddresses);

// Benzersiz e-posta adresleri için send_email işlevini çağırın
foreach ($uniqueEmailAddresses as $address) {
    $emailResult = send_email(trim($address), $subject, $mailBody);

    if (strpos($emailResult, 'E-posta gönderildi') !== false) {
        echo 'Event date successfully edited! E-posta gönderildi.'; 
        
    } else {
        echo 'Event date successfully edited! E-posta gönderilemedi: ' . $emailResult;
    }
}}
    header('Location: ../index.php');
    exit;}
	?>