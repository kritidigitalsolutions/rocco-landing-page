<?php
/**
 * Rocco Play Admin — AJAX: Upload Image
 * Receives file + category_id, validates, uploads, saves to DB
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$category_id = intval($_POST['category_id'] ?? 0);

if (!$category_id) {
    echo json_encode(['success' => false, 'message' => 'Category ID required.']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit;
}

$file = $_FILES['image'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validate size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File exceeds 5MB limit.']);
    exit;
}

// Validate MIME type server-side
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowedMimes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, WEBP allowed.']);
    exit;
}

// Create upload directory
$uploadDir = __DIR__ . '/uploads/images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_') . '_' . time() . '.' . strtolower($ext);
$filePath = $uploadDir . $filename;
$relativePath = 'admin/uploads/images/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
    exit;
}

// Get next display order
$stmt = $pdo->prepare("SELECT COALESCE(MAX(display_order), 0) + 1 as next_order FROM media WHERE category_id = :cid");
$stmt->execute(['cid' => $category_id]);
$nextOrder = $stmt->fetchColumn();

// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO media (category_id, filename, original_name, file_path, file_size, display_order, is_active, img_path, title) 
                           VALUES (:cid, :filename, :original, :filepath, :filesize, :display_order, 1, :imgpath, :title)");
    $stmt->execute([
        'cid' => $category_id,
        'filename' => $filename,
        'original' => $file['name'],
        'filepath' => $relativePath,
        'filesize' => $file['size'],
        'display_order' => $nextOrder,
        'imgpath' => $relativePath,
        'title' => pathinfo($file['name'], PATHINFO_FILENAME)
    ]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded.',
        'data' => [
            'id' => $newId,
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_path' => $relativePath,
            'file_size' => $file['size'],
            'display_order' => $nextOrder
        ]
    ]);
} catch (Exception $e) {
    // Remove file if DB insert fails
    if (file_exists($filePath)) unlink($filePath);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
