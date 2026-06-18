<?php
/**
 * Rocco Play Admin — AJAX: Reorder Categories
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$ids = $_POST['ids'] ?? [];

if (empty($ids) || !is_array($ids)) {
    echo json_encode(['success' => false, 'message' => 'No data received.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE categories SET display_order = :ord WHERE id = :id");
    foreach ($ids as $order => $id) {
        $stmt->execute(['ord' => $order + 1, 'id' => intval($id)]);
    }
    echo json_encode(['success' => true, 'message' => 'Category order updated.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
