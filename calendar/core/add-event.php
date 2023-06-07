<?php
require_once('../utils/auth.php');
require_once('../utils/sanitize.php');
require_once('../../vendor/autoload.php');
require_once('../../first.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}
// e-posta gönderme işlevi
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
        return 'E-posta gönderildi';
    } catch (Exception $e) {
        return 'E-posta gönderilemedi. Hata: ' . $mail->ErrorInfo;
    }
}

if (isset($_POST['title'])) {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $start = $_POST['start'];
    $startTime = $_POST['startTime'];
    $end = $_POST['end'];
    $endTime = $_POST['endTime'];

    $color = sanitizeInput($_POST['color']);
    $writtenby = $_POST["writtenby"];
    $responsible = isset($_POST['responsible']) ? implode(',', $_POST['responsible']) : '';

    // get email addresses for writtenby and responsible users
    $sql = "SELECT email FROM users WHERE username IN ('$writtenby', '$responsible')";
    $stmt = $auth->prepare($sql);
    $stmt->execute();
    $emailResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emails = array();
$writtenByEmail = ''; // Initialize a variable to store writtenby user's email

foreach ($emailResult as $key => $email) {
    if ($key == 0) {
        $writtenByEmail = $email['email']; // Store the writtenby user's email
    } else {
        $emails[] = $email['email'];
    }
}
if (isset($_FILES['file'])) {
    $errors = array();
    $file_array = reArrayFiles($_FILES['file']);
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_paths = array();
    foreach ($file_array as $file) {
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_type = $file['type'];
        $file_ext = strtolower(end(explode('.', $file['name'])));

        $extensions = array("jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx");

        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "Bu türde bir dosya yüklemeye izin verilmiyor, lütfen JPEG, PNG, PDF, DOC veya XLSX dosyası seçin.";
        }

        if (empty($errors) == true) {
            // Aynı isimde dosya varsa üzerine yazmamak için dosya adına zaman damgası ekleyin
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '_' . time() . '.' . $file_ext;

            move_uploaded_file($file_tmp, $upload_dir . $file_name);
            $file_paths[] = $upload_dir . $file_name;
        } else {
            print_r($errors);
        }
    }

    $file_path = implode(',', $file_paths);
} else {
    $file_path = '';
}

// insert event record
$sql = "INSERT INTO events(title, description, color, start,starttime, end,endtime, writtenby, responsible_persons, email_addresses, updated_by, file_path)
        values ('$title', '$description', '$color', '$start','$startTime', '$end','$endTime', '$writtenby', '$responsible', '" . implode(',', $emails) . "', '$writtenby', '$file_path')";


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
        // send e-mail to responsible persons and event creator
   // send e-mail to responsible persons and event creator
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
$subject = 'Un nouvel événement a été créé: ' . $title;
$body = "
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
            .event-dates {
                font-style: italic;
                color: #1c2331;
            }
        </style>
    </head>
    <body>
	<div class=\"container\">
    <h2>Un nouvel événement a été créé</h2>
    <p>L'événement suivant a été créé :</p>
    <p class=\"event-name\">Nom de l'événement : $title</p>
    <p class=\"event-name\">Description : $description</p>
    <p class=\"event-dates\">Dates : $start - $end</p>
    <p>Créé par : $writtenby</p>
    <p>Responsables : $responsible</p>
    <p class=\"event-name\">Added File : $file_name</p>

</div>
    </body>
    </html>
";
foreach ($emails as $email) {
    $result = send_email($email, $subject, $body);
    if (strpos($result, 'E-posta gönderildi')
	 === false) {
        echo 'E-posta gönderilemedi: ' . $result;
    }
}}
header('Location: '.$_SERVER['HTTP_REFERER']);
}
	?>