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

$action = $_POST['action'] ?? 'save';
$id = (int) ($_POST['id'] ?? 0);

try {
    if ($action === 'toggle_active') {
        $stmt = $pdo->prepare("UPDATE hero_slides SET is_active = :is_active WHERE id = :id");
        $stmt->execute(['is_active' => (int) ($_POST['is_active'] ?? 0), 'id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Hero slide status updated.']);
        exit;
    }

    if ($action === 'update_order') {
        $stmt = $pdo->prepare("UPDATE hero_slides SET display_order = :display_order WHERE id = :id");
        $stmt->execute(['display_order' => (int) ($_POST['display_order'] ?? 0), 'id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Hero slide order updated.']);
        exit;
    }

    $heading = trim($_POST['heading'] ?? '');
    if ($heading === '') {
        echo json_encode(['success' => false, 'message' => 'Heading is required.']);
        exit;
    }

    $imagePath = trim($_POST['existing_image_path'] ?? '');
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $maxSize = 5 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Image exceeds 5MB limit.']);
            exit;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimes, true)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and WEBP images are allowed.']);
            exit;
        }

        $uploadDir = __DIR__ . '/uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('hero_', true) . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded image.']);
            exit;
        }

        $imagePath = 'admin/uploads/images/' . $filename;
    }

    if ($imagePath === '') {
        echo json_encode(['success' => false, 'message' => 'Hero image is required.']);
        exit;
    }

    $data = [
        'image_path' => $imagePath,
        'badge_text' => trim($_POST['badge_text'] ?? ''),
        'badge_icon' => trim($_POST['badge_icon'] ?? 'fas fa-satellite-dish') ?: 'fas fa-satellite-dish',
        'heading' => $heading,
        'paragraph' => trim($_POST['paragraph'] ?? ''),
        'display_order' => (int) ($_POST['display_order'] ?? 0),
    ];

    if ($id > 0) {
        $data['id'] = $id;
        $stmt = $pdo->prepare("
            UPDATE hero_slides
            SET image_path = :image_path,
                badge_text = :badge_text,
                badge_icon = :badge_icon,
                heading = :heading,
                paragraph = :paragraph,
                display_order = :display_order
            WHERE id = :id
        ");
        $stmt->execute($data);
        echo json_encode(['success' => true, 'message' => 'Hero slide updated.']);
        exit;
    }

    if ($data['display_order'] <= 0) {
        $data['display_order'] = (int) $pdo->query("SELECT COALESCE(MAX(display_order), 0) + 1 FROM hero_slides")->fetchColumn();
    }
    $data['is_active'] = 1;

    $stmt = $pdo->prepare("
        INSERT INTO hero_slides
          (image_path, badge_text, badge_icon, heading, paragraph, display_order, is_active)
        VALUES
          (:image_path, :badge_text, :badge_icon, :heading, :paragraph, :display_order, :is_active)
    ");
    $stmt->execute($data);

    echo json_encode(['success' => true, 'message' => 'Hero slide added.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
