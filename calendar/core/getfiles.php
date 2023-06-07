<?php
    // Veritabanı bağlantısı
    require_once('./utils/auth.php');
    include "../database.php";
    // Verileri al
    $sql = "SELECT * FROM files";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON olarak verileri döndür
    header('Content-Type: application/json');
    echo json_encode($files);
?>