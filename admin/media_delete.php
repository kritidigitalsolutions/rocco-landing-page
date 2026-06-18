<?php
/**
 * Rocco Play Admin — AJAX: Delete Image
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid image ID.']);
    exit;
}

try {
    // Get file path before deleting
    $stmt = $pdo->prepare("SELECT file_path, img_path FROM media WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $media = $stmt->fetch();

    if (!$media) {
        echo json_encode(['success' => false, 'message' => 'Image not found.']);
        exit;
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM media WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Delete file from server  
    $path = $media['file_path'] ?: $media['img_path'];
    if ($path) {
        // Try relative to admin dir
        $fullPath = __DIR__ . '/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        // Try relative to root
        $rootPath = dirname(__DIR__) . '/' . $path;
        if (file_exists($rootPath)) {
            unlink($rootPath);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Image deleted.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
