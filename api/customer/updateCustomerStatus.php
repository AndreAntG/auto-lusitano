<?php
require_once '../cnn.php';
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id = intval($_REQUEST['id']);
    $sql = 'UPDATE customer SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = ['total' => $stmt->rowCount()];
} else {
    $data = ['total' => 0];
}
echo json_encode($data);
?>