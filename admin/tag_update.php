<?php
/**
 * Rocco Play Admin — AJAX: Update Tag / Toggle Active
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
$action = $_POST['action'] ?? 'update_tag';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid media ID.']);
    exit;
}

try {
    if ($action === 'toggle_active') {
        $isActive = intval($_POST['is_active'] ?? 0);
        $stmt = $pdo->prepare("UPDATE media SET is_active = :active WHERE id = :id");
        $stmt->execute(['active' => $isActive, 'id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Status updated.']);
    } else {
        $tag = trim($_POST['tag'] ?? '');
        $stmt = $pdo->prepare("UPDATE media SET tag = :tag, badge_text = :badge WHERE id = :id");
        $stmt->execute(['tag' => $tag, 'badge' => $tag, 'id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Tag updated.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
