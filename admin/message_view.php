<?php
$pageTitle = 'View Message';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: messages.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->execute([$id]);
$msg = $stmt->fetch();

if (!$msg) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Message not found.'];
    header("Location: messages.php");
    exit;
}

// Mark as read
if (!$msg['is_read']) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
}

$breadcrumb = [
    ['label' => 'Messages', 'url' => 'messages.php'],
    ['label' => 'View Message']
];
$currentPage = 'message_view';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="page-header">
<h1 class="page-title">Message Details</h1>
<div class="accent-bar"></div>
</div>

<div class="card" style="max-width: 800px;">
<div class="card-header-custom">
    <div class="card-title-custom"><i class="fas fa-envelope-open-text"></i> Message Details</div>
    <a href="messages.php" class="btn-admin btn-sm-admin btn-outline-admin"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
</div>

<div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.08);">
    <div style="display:flex; justify-content:space-between; margin-bottom: 10px;">
        <div>
            <strong style="color:var(--brand-gold);">From:</strong> <br>
            <?php echo htmlspecialchars($msg['name']); ?> &lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;
        </div>
        <div style="text-align: right;">
            <strong style="color:var(--brand-gold);">Date:</strong> <br>
            <?php echo date('F j, Y, g:i a', strtotime($msg['created_at'])); ?>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <strong style="color:var(--brand-gold);">Subject:</strong> <br>
        <?php echo htmlspecialchars($msg['subject'] ?? 'No Subject'); ?>
    </div>
</div>

<div style="background: var(--bg-input); padding: 20px; border-radius: 10px; border: 1px solid var(--glass-border); line-height: 1.8; font-size: 0.95rem; white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></div>

<div style="margin-top: 20px; display: flex; gap: 10px;">
    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn-admin btn-primary-admin"><i class="fas fa-reply"></i> Reply via Email</a>
    
    <form method="POST" action="messages.php" style="margin-left: auto;" onsubmit="return confirm('Are you sure you want to delete this message?');">
        <input type="hidden" name="delete_id" value="<?php echo $msg['id']; ?>">
        <button type="submit" class="btn-admin btn-danger-admin"><i class="fas fa-trash"></i> Delete</button>
    </form>
</div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
