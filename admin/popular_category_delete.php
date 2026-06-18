<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT bg_image, icon_image FROM popular_categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();
        if ($cat) {
            if ($cat['bg_image']) {
                $path = __DIR__ . '/../' . $cat['bg_image'];
                if (file_exists($path)) @unlink($path);
            }
            if ($cat['icon_image']) {
                $path = __DIR__ . '/../' . $cat['icon_image'];
                if (file_exists($path)) @unlink($path);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM popular_categories WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
