<?php
$pageTitle = 'Messages';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Message deleted successfully.'];
    header("Location: messages.php");
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

$breadcrumb = [
    ['label' => 'Messages']
];
$currentPage = 'messages';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="page-header">
<h1 class="page-title">Messages Inbox</h1>
<p class="page-subtitle">View and manage messages sent from the website contact form.</p>
</div>

<div class="card">
<div class="card-header-custom">
    <div class="card-title-custom"><i class="fas fa-envelope"></i> All Messages</div>
</div>

<?php if (count($messages) > 0): ?>
    <div class="table-responsive">
    <table class="table-admin">
        <thead>
        <tr>
            <th>Status</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <tr>
            <td>
                <?php if ($msg['is_read']): ?>
                    <span class="badge-admin" style="background: rgba(255,255,255,0.1); color: #aaa;"><i class="fas fa-envelope-open"></i> Read</span>
                <?php else: ?>
                    <span class="badge-admin" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71;"><i class="fas fa-envelope"></i> Unread</span>
                <?php endif; ?>
            </td>
            <td style="font-weight: <?php echo $msg['is_read'] ? '400' : 'bold'; ?>"><?php echo htmlspecialchars($msg['name']); ?></td>
            <td><?php echo htmlspecialchars($msg['email']); ?></td>
            <td style="font-weight: <?php echo $msg['is_read'] ? '400' : 'bold'; ?>"><?php echo htmlspecialchars(substr($msg['subject'] ?? 'No Subject', 0, 40)); ?>...</td>
            <td><?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></td>
            <td>
                <div style="display:flex; gap:8px;">
                    <a href="message_view.php?id=<?php echo $msg['id']; ?>" class="btn-admin btn-sm-admin btn-info-admin"><i class="fas fa-eye"></i> View</a>
                    <form method="POST" action="messages.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                        <input type="hidden" name="delete_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" class="btn-admin btn-sm-admin btn-danger-admin"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php else: ?>
    <div class="empty-state">
    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
    <div class="empty-state-text">Inbox is empty</div>
    <div class="empty-state-hint">You haven't received any messages yet.</div>
    </div>
<?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
