<?php
// get_cars_with_images.php - Get cars that have associated images

require_once '../cnn.php';
require_once '../session.php';

// Check if user is logged in (API style - return JSON instead of redirect)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

try {
    header('Content-Type: application/json');

    // Test database connection
    if (!isset($pdo)) {
        throw new Exception('Database connection not available');
    }

    // Get cars that have images
    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.make,
            c.model,
            c.year,
            c.price,
            c.description,
            c.image_filename,
            c.status,
            c.is_for_sale,
            c.is_for_rent,
            c.daily_rent_price,
            cu.name as owner_name
        FROM cars c
        LEFT JOIN customer cu ON c.owner_id = cu.id
        WHERE c.image_filename IS NOT NULL
        AND c.image_filename != ''
        ORDER BY c.created_at DESC
    ");

    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'cars' => $cars,
        'count' => count($cars)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit();
}
?>