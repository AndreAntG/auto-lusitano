<?php
// upload_image.php - Handle image uploads for cars

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

// Check if car exists
try {
    $stmt = $pdo->prepare("SELECT image_filename FROM cars WHERE id = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Car not found']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['car_image']) || $_FILES['car_image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No image file uploaded or upload error']);
    exit();
}

$file = $_FILES['car_image'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
    exit();
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit();
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'car_' . $carId . '_' . time() . '.' . $extension;

// Define upload directory
$uploadDir = __DIR__ . '/../../images/';
$uploadPath = $uploadDir . $filename;

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Delete old image if it exists
if (!empty($car['image_filename'])) {
    $oldImagePath = __DIR__ . '/../../images/' . $car['image_filename'];
    if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
    }
}

// Move uploaded file
error_log("Upload debug - uploadDir: $uploadDir, uploadPath: $uploadPath, tmp_name: " . $file['tmp_name']);
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    error_log("Upload failed - uploadPath: $uploadPath, file exists: " . (file_exists($uploadPath) ? 'yes' : 'no'));
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save image file']);
    exit();
}
error_log("Upload success - file saved to: $uploadPath");

// Update database with new filename
try {
    $stmt = $pdo->prepare("UPDATE cars SET image_filename = ? WHERE id = ?");
    $stmt->execute([$filename, $carId]);

    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'filename' => $filename
    ]);

} catch (PDOException $e) {
    // If database update fails, delete the uploaded file
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit();
}