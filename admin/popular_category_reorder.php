<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ids'] ?? [];
    if (is_array($ids)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE popular_categories SET display_order = ? WHERE id = ?");
            foreach ($ids as $index => $id) {
                // $index is 0-based
                $stmt->execute([$index + 1, $id]);
            }
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
}
