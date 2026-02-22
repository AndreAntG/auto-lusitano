<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("EditSale Debug - Data received: " . print_r($data, true));

if (isset($data['id']) && $data['id'] != '' && isset($data['car_id']) && $data['car_id'] != '' && isset($data['seller_id']) && $data['seller_id'] != '' && isset($data['buyer_id']) && $data['buyer_id'] != '' && isset($data['sale_price']) && $data['sale_price'] != '') {
    // Start transaction
    $pdo->beginTransaction();

    try {
        // Get the old car_id to potentially update its status back
        $old_sale_sql = 'SELECT car_id FROM sales WHERE id = :id';
        $old_sale_stmt = $pdo->prepare($old_sale_sql);
        $old_sale_stmt->execute([':id' => intval($data['id'])]);
        $old_sale = $old_sale_stmt->fetch();

        // Update the sale
        $sql = 'UPDATE sales SET car_id = :car_id, seller_id = :seller_id, buyer_id = :buyer_id, sale_price = :sale_price, sale_date = :sale_date WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':id' => intval($data['id']),
            ':car_id' => intval($data['car_id']),
            ':seller_id' => intval($data['seller_id']),
            ':buyer_id' => intval($data['buyer_id']),
            ':sale_price' => floatval($data['sale_price']),
            ':sale_date' => isset($data['sale_date']) && $data['sale_date'] != '' ? $data['sale_date'] : date('Y-m-d H:i:s')
        ];
        error_log("EditSale Debug - Params: " . print_r($params, true));
        $stmt->execute($params);

        // If car changed, update statuses
        if ($old_sale && $old_sale['car_id'] != $data['car_id']) {
            // Set old car back to available
            $reset_sql = 'UPDATE cars SET status = "available" WHERE id = :old_car_id';
            $reset_stmt = $pdo->prepare($reset_sql);
            $reset_stmt->execute([':old_car_id' => intval($old_sale['car_id'])]);

            // Set new car to sold
            $update_sql = 'UPDATE cars SET status = "sold" WHERE id = :new_car_id';
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([':new_car_id' => intval($data['car_id'])]);
        }

        $pdo->commit();
        $response['msg'] = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("EditSale Error: " . $e->getMessage());
        $response['msg'] = false;
    }
}
echo json_encode($response);
?>