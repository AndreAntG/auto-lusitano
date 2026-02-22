<?php
require_once '../cnn.php';
$includeInactive = isset($_GET['includeInactive']) && $_GET['includeInactive'] === 'true';
$statusCondition = $includeInactive ? '' : 'WHERE status = 1';
$sql = 'SELECT id, name, email, phone, address, created_at, status FROM customer ' . $statusCondition . ' ORDER BY name';
$stmt = $pdo->query($sql);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($customers);
?>