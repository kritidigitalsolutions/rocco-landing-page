<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/hero_slides_schema.php';
requireAuth();
header('Content-Type: application/json');

ensureHeroSlidesTable($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
    exit;
}

$ids = $_POST['ids'] ?? [];
if (empty($ids) || !is_array($ids)) {
    echo json_encode(['success' => false, 'message' => 'No order data received.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE hero_slides SET display_order = :display_order WHERE id = :id");
    foreach ($ids as $order => $id) {
        $stmt->execute([
            'display_order' => $order + 1,
            'id' => (int) $id,
        ]);
    }
    echo json_encode(['success' => true, 'message' => 'Hero slide order updated.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
