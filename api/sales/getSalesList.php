<?php
require_once '../cnn.php';
$sql = 'SELECT s.id, s.car_id, s.seller_id, s.buyer_id, s.sale_price, s.sale_date,
               c.make as car_make, c.model as car_model, c.year as car_year,
               seller.name as seller_name, buyer.name as buyer_name
        FROM sales s
        JOIN cars c ON s.car_id = c.id
        JOIN customer seller ON s.seller_id = seller.id
        JOIN customer buyer ON s.buyer_id = buyer.id
        ORDER BY s.sale_date DESC';
$stmt = $pdo->query($sql);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($sales);
?>