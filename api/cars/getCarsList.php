<?php
require_once '../../cnn.php';
$includeUnavailable = isset($_GET['includeUnavailable']) && $_GET['includeUnavailable'] === 'true';
$statusCondition = $includeUnavailable ? '' : "WHERE status = 'available'";
$sql = 'SELECT id, make, model, year, price, status, is_for_sale, is_for_rent, daily_rent_price, image_filename FROM cars ' . $statusCondition . ' ORDER BY make, model';
$stmt = $pdo->query($sql);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cars);
?>