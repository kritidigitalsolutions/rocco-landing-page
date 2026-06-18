<?php
/**
 * Rocco Play Admin — AJAX: Save Settings
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$action = $_POST['action'] ?? '';
$section = $_POST['section'] ?? '';

try {
    // Handle special actions
    if ($action === 'reset') {
        $pdo->exec("UPDATE site_settings SET copyright_text='', playstore_link='', appstore_link='', custom_app_link='', gtm_code='', ga_code='' WHERE id=1");
        echo json_encode(['success' => true, 'message' => 'Settings reset to defaults.']);
        exit;
    }

    if ($action === 'clear_media') {
        // Delete all files from upload directory
        $uploadDir = __DIR__ . '/uploads/images/';
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        $pdo->exec("DELETE FROM media");
        echo json_encode(['success' => true, 'message' => 'All media cleared.']);
        exit;
    }

    // Save by section
    if ($section === 'general') {
        $copyright = $_POST['copyright_text'] ?? '';
        $stmt = $pdo->prepare("UPDATE site_settings SET copyright_text = :val WHERE id = 1");
        $stmt->execute(['val' => $copyright]);
    } elseif ($section === 'applinks') {
        $playstore = trim($_POST['playstore_link'] ?? '');
        $appstore = trim($_POST['appstore_link'] ?? '');
        $custom = trim($_POST['custom_app_link'] ?? '');
        $stmt = $pdo->prepare("UPDATE site_settings SET playstore_link = :play, appstore_link = :app, custom_app_link = :custom WHERE id = 1");
        $stmt->execute(['play' => $playstore, 'app' => $appstore, 'custom' => $custom]);
    } elseif ($section === 'analytics') {
        $gtm = $_POST['gtm_code'] ?? '';
        $ga = $_POST['ga_code'] ?? '';
        $stmt = $pdo->prepare("UPDATE site_settings SET gtm_code = :gtm, ga_code = :ga WHERE id = 1");
        $stmt->execute(['gtm' => $gtm, 'ga' => $ga]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown section.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Settings saved successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
