<?php
/**
 * Rocco Play Admin — Login Page
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$error = '';
$flash_login = $_SESSION['flash_login'] ?? null;
unset($_SESSION['flash_login']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate CSRF
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Rocco Play Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="icon" type="image/jpeg" href="../img/logo.jpg">
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <div class="login-logo">
        <img src="../img/logo.jpg" alt="Rocco Play">
        <h1>RoccoPlay</h1>
        <p>Admin Dashboard</p>
      </div>

      <?php if ($flash_login): ?>
      <div style="background:rgba(46,204,113,0.1);border:1px solid rgba(46,204,113,0.2);border-radius:10px;padding:12px 16px;color:#2ecc71;font-size:0.85rem;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-check-circle"></i>
        <span><?php echo htmlspecialchars($flash_login); ?></span>
      </div>
      <?php endif; ?>

      <div class="login-error <?php echo $error ? 'show' : ''; ?>" id="loginError">
        <i class="fas fa-exclamation-circle"></i>
        <span id="loginErrorText"><?php echo htmlspecialchars($error); ?></span>
      </div>

      <form method="POST" action="login.php" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-icon-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" class="form-control-admin" placeholder="admin@roccoplay.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" style="display:flex;justify-content:space-between;align-items:center;">
            <span>Password</span>
            <a href="forgot_password.php" style="font-size:0.78rem;color:var(--brand-gold);font-weight:500;transition:opacity 0.2s ease;text-decoration:none;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
              <i class="fas fa-key" style="margin-right:4px;font-size:0.7rem;"></i>Forgot Password?
            </a>
          </label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" class="form-control-admin" placeholder="Enter your password" required autocomplete="current-password">
          </div>
        </div>

        <button type="submit" class="login-btn">
          <i class="fas fa-right-to-bracket"></i> Sign In
        </button>
      </form>
    </div>
  </div>
</body>
</html>
