<?php
// Script to update car descriptions to European Portuguese (pt-PT)
// This script corrects any Brazilian Portuguese terms to European Portuguese

require_once 'cnn.php'; // Include database connection

try {
    // Update the description that contains Brazilian Portuguese term 'esportivo' to European Portuguese 'desportivo'
    $stmt = $pdo->prepare("UPDATE cars SET description = 'Carro desportivo' WHERE description = 'Carro esportivo'");
    $result = $stmt->execute();

    if ($result) {
        echo "Successfully updated car descriptions to European Portuguese (pt-PT).\n";
        echo "Changed 'Carro esportivo' to 'Carro desportivo'.\n";
    } else {
        echo "No updates were needed - descriptions may already be in European Portuguese.\n";
    }

    // Verify all descriptions
    $stmt = $pdo->query("SELECT id, make, model, description FROM cars WHERE description LIKE '%esportivo%' OR description LIKE '%desportivo%'");
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($cars) > 0) {
        echo "\nCars with updated descriptions:\n";
        foreach ($cars as $car) {
            echo "- {$car['make']} {$car['model']}: {$car['description']}\n";
        }
    } else {
        echo "\nAll car descriptions are now in European Portuguese.\n";
    }

} catch (PDOException $e) {
    echo "Error updating car descriptions: " . $e->getMessage() . "\n";
}
?>