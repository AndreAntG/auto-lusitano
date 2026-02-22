<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("InsertSale Debug - Data received: " . print_r($data, true));

if (isset($data['car_id']) && $data['car_id'] != '' && isset($data['seller_id']) && $data['seller_id'] != '' && isset($data['buyer_id']) && $data['buyer_id'] != '' && isset($data['sale_price']) && $data['sale_price'] != '') {
    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert the sale
        $sql = 'INSERT INTO sales (car_id, seller_id, buyer_id, sale_price, sale_date) VALUES (:car_id, :seller_id, :buyer_id, :sale_price, :sale_date)';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':car_id' => intval($data['car_id']),
            ':seller_id' => intval($data['seller_id']),
            ':buyer_id' => intval($data['buyer_id']),
            ':sale_price' => floatval($data['sale_price']),
            ':sale_date' => isset($data['sale_date']) && $data['sale_date'] != '' ? $data['sale_date'] : date('Y-m-d H:i:s')
        ];
        error_log("InsertSale Debug - Params: " . print_r($params, true));
        $stmt->execute($params);

        // Update car status to sold
        $update_sql = 'UPDATE cars SET status = "sold" WHERE id = :car_id';
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([':car_id' => intval($data['car_id'])]);

        $pdo->commit();
        $response['msg'] = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("InsertSale Error: " . $e->getMessage());
        $response['msg'] = false;
    }
}
echo json_encode($response);
?>