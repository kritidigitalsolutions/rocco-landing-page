<?php
/**
 * Rocco Play Admin — AJAX: Delete Category
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid category ID.']);
    exit;
}

try {
    // First delete associated media files from server
    $stmt = $pdo->prepare("SELECT file_path FROM media WHERE category_id = :id");
    $stmt->execute(['id' => $id]);
    $files = $stmt->fetchAll();
    
    foreach ($files as $file) {
        if ($file['file_path'] && file_exists(__DIR__ . '/' . $file['file_path'])) {
            unlink(__DIR__ . '/' . $file['file_path']);
        }
    }

    // Delete category (media deleted via CASCADE)
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
