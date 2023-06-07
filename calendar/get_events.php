<?php
// Connect Your database
$pdo = new PDO('mysql:host=localhost;dbname=offer', 'root', '');

// Select your table
$stmt = $pdo->prepare('SELECT * FROM events');
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sonuçları JSON olarak döndürün
header('Content-Type: application/json');
echo json_encode($events);
?>