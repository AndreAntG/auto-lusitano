<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("EditRental Debug - Data received: " . print_r($data, true));

if (isset($data['id']) && $data['id'] != '' && isset($data['car_id']) && $data['car_id'] != '' && isset($data['renter_id']) && $data['renter_id'] != '' && isset($data['start_date']) && $data['start_date'] != '' && isset($data['end_date']) && $data['end_date'] != '' && isset($data['total_price']) && $data['total_price'] != '') {
    try {
        // Update the rental
        $sql = 'UPDATE rentals SET car_id = :car_id, renter_id = :renter_id, start_date = :start_date, end_date = :end_date, total_price = :total_price, status = :status WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':id' => intval($data['id']),
            ':car_id' => intval($data['car_id']),
            ':renter_id' => intval($data['renter_id']),
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':total_price' => floatval($data['total_price']),
            ':status' => isset($data['status']) && $data['status'] != '' ? $data['status'] : 'active'
        ];
        error_log("EditRental Debug - Params: " . print_r($params, true));
        $stmt->execute($params);

        $response['msg'] = true;
    } catch (Exception $e) {
        error_log("EditRental Error: " . $e->getMessage());
        $response['msg'] = false;
    }
}
echo json_encode($response);
?>