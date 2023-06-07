<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../utils/auth.php');
require_once('../../vendor/autoload.php');
require_once('../../first.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$editby =$_SESSION["username"];


function log_update_status($message) {
    $log_file = 'update_log.txt';
    $current_time = date('Y-m-d H:i:s');
    $log_message = "[$current_time] $message" . PHP_EOL;
    $result = file_put_contents($log_file, $log_message, FILE_APPEND);

    if ($result === false) {
        return 'error_writing_log';
    }
    return 'success';
}

function send_email($emailAddresses, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        $mail->isSMTP();
        $mail->Host       = 'ssl://smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gokhanbasturk12@gmail.com'; // Gönderen e-posta adresi
        $mail->Password   = 'YOUR PASSWORD'; // Gönderen e-posta şifresi
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 465;

        $mail->setFrom('gokhanbasturk12@gmail.com', 'Feurst Calendar');

        // Split the email addresses into an array
        $email_addresses_array = explode(',', $emailAddresses);

        // Loop through each email address and send a separate email
        foreach ($email_addresses_array as $email_address) {
            // Add the current email address to the email
            $mail->addAddress(trim($email_address));

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            // Clear the recipients list
            $mail->clearAddresses();
        }

        // Log the email status

        return 'E-posta gönderildi';
    } catch (Exception $e) {
        $error_message = 'E-posta gönderilemedi. Hata: ' . $mail->ErrorInfo;

        // Log the email status

        return $error_message;
    }
}



// Diğer PHP kodları

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $writtenBy = $_POST['writtenby'];
    $sessionEditBy = $_POST['sessionEditBy'];

    if ($writtenBy == $sessionEditBy) {
        $sql = "DELETE FROM events WHERE id = $id";
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
    }
} else if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['color']) && isset($_POST['id']) && isset($_POST['start']) && isset($_POST['end'])) {

    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $color = $_POST['color'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $writtenby = $_POST["writtenby"];
    $responsible_persons = isset($_POST['responsible_edit']) ? implode(",", $_POST['responsible_edit']) : '';

    $uploaded_files = '';
    if (isset($_FILES['edit-file']) && !empty($_FILES['edit-file']['name'][0])) {
        $errors = array();
        $files = $_FILES['edit-file'];
    
        for ($i = 0; $i < count($files['name']); $i++) {
            $file_name = $files['name'][$i];
            $file_size = $files['size'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_type = $files['type'][$i];
    
            if ($file_size > 16777216) {
                $errors[] = 'File size must be less than 5 MB';
                
            }
            
            if (empty($errors)) {
                $file_name_parts = pathinfo($file_name);
                $file_ext = $file_name_parts['extension'];
                date_default_timezone_set('Europe/Paris');
                $file_new_name = pathinfo($file_name, PATHINFO_FILENAME) . '_' . time() . '.' . $file_ext;
                move_uploaded_file($file_tmp, "uploads/" . $file_new_name);
                if ($uploaded_files == '') {
                    $uploaded_files .= $file_new_name;
                } else {
                    $uploaded_files .= ',' . $file_new_name;
                }
            } else {
                print_r($errors);
            }
        }
    }
    
    // Eski dosyaları koruyarak yeni yüklenen dosyaları ekleyin
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $current_event_query = $auth->prepare("SELECT * FROM events WHERE id = :id");
        $current_event_query->bindParam(':id', $id);
        $current_event_query->execute();
        $current_event = $current_event_query->fetch(PDO::FETCH_ASSOC);
        $current_file_path = $current_event['file_path'];
    }
    // Merge the current file path with the uploaded file names
    $file_path = '';
    if ($current_file_path != '') {
        $file_path .= $current_file_path;
    }
    if ($uploaded_files != '') {
        $uploaded_file_array = explode(',', $uploaded_files);
        foreach ($uploaded_file_array as $uploaded_file) {
            if ($file_path != '') {
                $file_path .= ',' . 'uploads/' . $uploaded_file;
            } else {
                $file_path .= 'uploads/' . $uploaded_file;
            }
        }
    } elseif ($file_path == '' && empty($_FILES['edit-file']['name'][0])) {
        $file_path = '';
    }

    $current_event_query = $auth->prepare("SELECT * FROM events WHERE id = :id");
    $current_event_query->bindParam(':id', $id);
    $current_event_query->execute();
    $current_event = $current_event_query->fetch(PDO::FETCH_ASSOC);
    
    // Değerlerin aynı olup olmadığını kontrol edin
    $current_event_query = $auth->prepare("SELECT * FROM events WHERE id = :id");
$current_event_query->bindParam(':id', $id);
$current_event_query->execute();
$current_event = $current_event_query->fetch(PDO::FETCH_ASSOC);

// Değerlerin aynı olup olmadığını kontrol edin
$no_changes_made = (
    $title == $current_event['title'] &&
    $description == $current_event['description'] &&
    $color == $current_event['color'] &&
    $start == $current_event['start'] &&
    $end == $current_event['end'] &&
    $writtenby == $current_event['writtenby'] &&
    $responsible_persons == $current_event['responsible_persons'] &&
    $file_path == $current_event['file_path']
);

// Değerler aynıysa, hiçbir şey yapmayın ve sayfayı yönlendirin
if ($no_changes_made) {
    header('Location: ../index.php');
    exit;
}
   // Retrieve the email addresses for the responsible persons from the users table
$responsible_persons_emails = array();
$stmt = $auth->prepare("SELECT email FROM users WHERE username IN ('$writtenby', '$responsible_persons')");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$first_email = true;
foreach ($rows as $row) {
    if ($first_email && $editby == $writtenby) {
        $first_email = false;
    } else {
        $responsible_persons_emails[] = $row['email'];
        $first_email = false;
    }
}
$email_addresses = implode(",", $responsible_persons_emails);

$sql = "UPDATE events SET title = '$title', start = '$start', end = '$end', description = '$description', color = '$color', writtenby = '$writtenby', responsible_persons = '$responsible_persons', email_addresses = '$email_addresses', updated_by = '$editby', file_path = '$file_path' WHERE id = $id ";

    $prepareQuery = $auth->prepare($sql);

    if ($prepareQuery == false) {
        print_r($auth->errorInfo());
        die('Error preparing the query.');
    }

    $executeQuery = $prepareQuery->execute();

    if ($executeQuery == false) {
        print_r($prepareQuery->errorInfo());
        die('Error executing the query.');
    } else {
  	$update_message = "Event successfully updated! ID: $id, Title: $title, Description: $description, Start: $start, End: $end, Written by: $writtenby, Responsible persons: $responsible_persons";
    log_update_status($update_message);
        // Compose the email message
        $file_name = trim(substr($file_path, strrpos($file_path, '/') + 1), " \t\n\r\0\x0B");
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
        
        // Sonundaki sayıları kaldırmak için regular expression kullanarak işlem yapabilirsiniz
        $file_name = preg_replace('/_[0-9]+\./', '.', $file_name);
        
        // "_" karakterlerini boşluk karakterleriyle değiştirin
        $file_name = str_replace("_", " ", $file_name);
        
        // Dosya adındaki boşlukları, sekme, satırbaşı vb. karakterleri kaldırın
        $file_name = preg_replace('/\s+/', ' ', $file_name);
        
        // Dosya adının baş harfini büyük harf yapın
        $file_name = ucfirst($file_name);
        
        // Tamamlanan dosya adını birleştirin
        $file_name = $file_name ;
        $subject = "Événement mis à jour avec succès";
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
					<p class=\"event-name\">Nom de l'événement : $title</p>
					<p class=\"event-name\">Description : $description</p>
                    <p class=\"write-name\">Mis à jour par $editby</p>
                    <p class=\"event-dates\">Dates : $start → $end</p>
                    <p class=\"event-name\">Added File : $file_name</p>

                </div>
            </body>
            </html>
        ";
  // Send email to responsible persons
  $emailResult = send_email($email_addresses, $subject, $mailBody);

  // Check if email was sent successfully
  if (strpos($emailResult, 'E-posta gönderildi') !== false) {
	  echo 'Event successfully updated! E-posta gönderildi.'; 
  } else {
	  echo 'Event successfully updated! E-posta gönderilemedi: ' . $emailResult;
  }
}
}

header('Location: ../index.php');
?>