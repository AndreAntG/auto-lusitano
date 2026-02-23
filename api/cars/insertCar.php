<?php
require_once '../../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("InsertCar Debug - Data received: " . print_r($data, true));

if (isset($data['make']) && $data['make'] != '' && isset($data['model']) && $data['model'] != '' && isset($data['year']) && $data['year'] != '' && isset($data['price']) && $data['price'] != '') {
    $sql = 'INSERT INTO cars (make, model, year, price, description, status, is_for_sale, is_for_rent, daily_rent_price) VALUES (:make, :model, :year, :price, :description, :status, :is_for_sale, :is_for_rent, :daily_rent_price)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':make' => $data['make'],
        ':model' => $data['model'],
        ':year' => intval($data['year']),
        ':price' => floatval($data['price']),
        ':description' => $data['description'] ?? '',
        ':status' => $data['status'] ?? 'available',
        ':is_for_sale' => isset($data['is_for_sale']) ? 1 : 0,
        ':is_for_rent' => isset($data['is_for_rent']) ? 1 : 0,
        ':daily_rent_price' => isset($data['daily_rent_price']) && $data['daily_rent_price'] != '' ? floatval($data['daily_rent_price']) : null
    ]);
    $response['msg'] = true;
}
echo json_encode($response);
?>