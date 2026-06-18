<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $is_hidden = $_POST['is_hidden'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE popular_categories SET is_hidden = ? WHERE id = ?");
        $stmt->execute([$is_hidden, $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
