<?php
/**
 * Rocco Play Admin — AJAX: Update Media Metadata
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

// Verify CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$rating = floatval($_POST['rating'] ?? 0);
$year_or_seasons = trim($_POST['year_or_seasons'] ?? '');
$genre = trim($_POST['genre'] ?? '');

// Fetch old record
$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$id]);
$oldMedia = $stmt->fetch();
if (!$oldMedia) {
    echo json_encode(['success' => false, 'message' => 'Media not found']);
    exit;
}

$newImageUrl = null;
$filePath = $oldMedia['file_path'];
$fileSize = $oldMedia['file_size'];

// Handle Image Upload or Removal
if (!empty($_POST['remove_image']) && $_POST['remove_image'] == '1') {
    // Delete old file if exists locally
    if ($oldMedia['file_path'] && str_starts_with($oldMedia['file_path'], 'admin/uploads/images/')) {
        $oldLocalPath = __DIR__ . '/uploads/images/' . basename($oldMedia['file_path']);
        if (file_exists($oldLocalPath)) @unlink($oldLocalPath);
    }
    $filePath = '';
    $fileSize = 0;
    // Set a placeholder so frontend shows empty
    $newImageUrl = '../img/placeholder.jpg'; 
} elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    if (in_array(strtolower($file['type']), $allowed)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('img_') . '_' . time() . '.' . $ext;
        $destPath = __DIR__ . '/uploads/images/' . $newFilename;
        $dbPath = 'admin/uploads/images/' . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            // Delete old file if exists locally
            if ($oldMedia['file_path'] && str_starts_with($oldMedia['file_path'], 'admin/uploads/images/')) {
                $oldLocalPath = __DIR__ . '/uploads/images/' . basename($oldMedia['file_path']);
                if (file_exists($oldLocalPath)) @unlink($oldLocalPath);
            }
            
            $filePath = $dbPath;
            $fileSize = $file['size'];
            $newImageUrl = '../' . $dbPath; // URL for dashboard preview
        }
    }
}

try {
    $stmt = $pdo->prepare("UPDATE media SET title = ?, rating = ?, year_or_seasons = ?, genre = ?, file_path = ?, file_size = ? WHERE id = ?");
    $stmt->execute([$title, $rating, $year_or_seasons, $genre, $filePath, $fileSize, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Metadata updated successfully',
        'new_image_url' => $newImageUrl
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
