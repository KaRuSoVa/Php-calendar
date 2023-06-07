<?php
include "database.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log1.txt');
error_reporting(E_ALL);

if (isset($_POST['id']) && isset($_POST['file_path'])) {
    $event_id = $_POST['id'];
    $file_path = $_POST['file_path'];

    try {
        $sql = "SELECT file_path FROM events WHERE id = :event_id";
        $stmt = $auth->prepare($sql); // Change $pdo to $auth
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $file_paths = explode(",", $row['file_path']);

            if (($key = array_search($file_path, $file_paths)) !== false) {
                unset($file_paths[$key]);

                $core_file_path = 'core/' . $file_path;                   
                if (file_exists($core_file_path)) {
                    if (is_writable($core_file_path)) {
                        unlink($core_file_path);
                    } else {
                        error_log("Hata: Dosya yazılabilir değil - " . $core_file_pathh . "\n", 3, "error_log1.txt");
                        echo "The file is not writable, could not be deleted.";
                    }
                } else {
                    error_log("Hata: Dosya sunucuda bulunamadı - " . $core_file_path . "\n", 3, "error_log1.txt");
                    echo "File not found on server.";
                }

                $new_file_paths = implode(",", $file_paths);

                $sql = "UPDATE events SET file_path = :file_path WHERE id = :event_id";
                $stmt = $auth->prepare($sql); // Change $pdo to $auth
                $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
                $stmt->bindParam(':file_path', $new_file_paths, PDO::PARAM_STR);
                $stmt->execute();
                echo "File successfully deleted.";
            } else {
                echo "The file path does not exist.";
            }
        } else {
            echo "Event not found.";
        }
    } catch (PDOException $e) {
        error_log("Hata: " . $e->getMessage() . "\n", 3, "error_log1.txt");
        echo "An error occurred while deleting the file.";
    }
} else {
    echo "Required parameters are missing.";
}

?>