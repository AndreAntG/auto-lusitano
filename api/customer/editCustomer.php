<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;

// Debug output
error_log("EditClient Debug - Data received: " . print_r($data, true));

if (isset($data['id']) && $data['id'] != '' && isset($data['name']) && $data['name'] != '' && isset($data['email']) && $data['email'] != '') {
    $sql = 'UPDATE customer SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $ok = $stmt->execute();
    $response['msg'] = $ok;
    
    // Debug output
    error_log("EditClient Debug - SQL executed, result: " . ($ok ? 'success' : 'failed'));
    $response['debug'] = [
        'data_received' => $data,
        'sql_executed' => $sql,
        'execution_result' => $ok
    ];
} else {
    // Debug output for validation failure
    error_log("EditClient Debug - Validation failed. ID: " . ($data['id'] ?? 'not set') . ", Name: " . ($data['name'] ?? 'not set') . ", Email: " . ($data['email'] ?? 'not set'));
    $response['debug'] = [
        'validation_error' => 'Missing required fields',
        'id' => $data['id'] ?? 'not set',
        'name' => $data['name'] ?? 'not set', 
        'email' => $data['email'] ?? 'not set'
    ];
}

echo json_encode($response);
?>