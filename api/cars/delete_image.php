<?php
// delete_image.php - Handle image deletion for cars

require_once '../../cnn.php';
require_once '../../session.php';

// Check if user is logged in (API style - return JSON instead of redirect)
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

try {

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get car ID from request
$carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;

if (!$carId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Car ID is required']);
    exit();
}

// Check if car exists and get current image filename
try {
    $stmt = $pdo->prepare("SELECT image_filename FROM cars WHERE id = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Car not found']);
        exit();
    }

    if (empty($car['image_filename'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Car has no image to delete']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Delete the image file
$imagePath = __DIR__ . '/../../images/' . $car['image_filename'];
if (file_exists($imagePath)) {
    if (!unlink($imagePath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete image file']);
        exit();
    }
}

// Update database to remove image filename
try {
    $stmt = $pdo->prepare("UPDATE cars SET image_filename = NULL WHERE id = ?");
    $stmt->execute([$carId]);

    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit();
}