<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("EditCar Debug - Data received: " . print_r($data, true));

if (isset($data['id']) && $data['id'] != '' && isset($data['make']) && $data['make'] != '' && isset($data['model']) && $data['model'] != '' && isset($data['year']) && $data['year'] != '' && isset($data['price']) && $data['price'] != '') {
    $sql = 'UPDATE cars SET make = :make, model = :model, year = :year, price = :price, description = :description, status = :status, is_for_sale = :is_for_sale, is_for_rent = :is_for_rent, daily_rent_price = :daily_rent_price WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => intval($data['id']),
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