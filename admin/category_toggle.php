<?php
/**
 * Rocco Play Admin — AJAX: Toggle Category Visibility
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
$is_visible = intval($_POST['is_visible'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid category ID.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE categories SET is_visible = :vis, is_hidden = :hid WHERE id = :id");
    $stmt->execute([
        'vis' => $is_visible,
        'hid' => $is_visible ? 0 : 1,
        'id' => $id
    ]);
    echo json_encode(['success' => true, 'message' => 'Category visibility updated.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
