<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("InsertRental Debug - Data received: " . print_r($data, true));

if (isset($data['car_id']) && $data['car_id'] != '' && isset($data['renter_id']) && $data['renter_id'] != '' && isset($data['start_date']) && $data['start_date'] != '' && isset($data['end_date']) && $data['end_date'] != '' && isset($data['total_price']) && $data['total_price'] != '') {
    try {
        // Insert the rental
        $sql = 'INSERT INTO rentals (car_id, renter_id, start_date, end_date, total_price, status) VALUES (:car_id, :renter_id, :start_date, :end_date, :total_price, :status)';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':car_id' => intval($data['car_id']),
            ':renter_id' => intval($data['renter_id']),
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':total_price' => floatval($data['total_price']),
            ':status' => isset($data['status']) && $data['status'] != '' ? $data['status'] : 'active'
        ];
        error_log("InsertRental Debug - Params: " . print_r($params, true));
        $stmt->execute($params);

        $response['msg'] = true;
    } catch (Exception $e) {
        error_log("InsertRental Error: " . $e->getMessage());
        $response['msg'] = false;
    }
}
echo json_encode($response);
?>