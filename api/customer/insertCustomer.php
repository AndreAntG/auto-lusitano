<?php
require_once '../cnn.php';
$data = $_REQUEST;
$response['msg'] = false;
if (isset($data['name']) && $data['name'] != '' && isset($data['email']) && $data['email'] != '') {
    $sql = 'INSERT INTO customer (name, email, phone, address) VALUES (:name, :email, :phone, :address)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
    $ok = $stmt->execute();
    $response['msg'] = $ok;
}
echo json_encode($response);
?>