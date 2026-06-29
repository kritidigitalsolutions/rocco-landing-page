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

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid hero slide ID.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $slide = $stmt->fetch();

    if (!$slide) {
        echo json_encode(['success' => false, 'message' => 'Hero slide not found.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $path = $slide['image_path'] ?? '';
    if (str_starts_with($path, 'admin/uploads/images/')) {
        $fullPath = dirname(__DIR__) . '/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Hero slide deleted.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
