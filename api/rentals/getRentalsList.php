<?php
require_once '../cnn.php';
$includeCompleted = isset($_GET['includeCompleted']) && $_GET['includeCompleted'] === 'true';
$statusCondition = $includeCompleted ? '' : "WHERE r.status = 'active'";
$sql = 'SELECT r.id, r.car_id, r.renter_id, r.start_date, r.end_date, r.total_price, r.status,
               c.make as car_make, c.model as car_model, c.year as car_year,
               cust.name as renter_name
        FROM rentals r
        JOIN cars c ON r.car_id = c.id
        JOIN customer cust ON r.renter_id = cust.id
        ' . $statusCondition . '
        ORDER BY r.start_date DESC';
$stmt = $pdo->query($sql);
$rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rentals);
?>