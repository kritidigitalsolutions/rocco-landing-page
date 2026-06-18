<?php
/**
 * Rocco Play Admin — Forgot Password (OTP via Email)
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$error = '';
$success = '';
$step = 'email'; // email → otp → reset

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        // ===== STEP 1: Send OTP to admin email =====
        if ($action === 'send_otp') {
            $email = trim($_POST['email'] ?? '');
            if (empty($email)) {
                $error = 'Please enter your email address.';
            } else {
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Generate 6-digit OTP
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                    // Store OTP in database
                    $stmt = $pdo->prepare("UPDATE admin_users SET reset_otp = :otp, reset_otp_expires = :expires WHERE id = :id");
                    $stmt->execute(['otp' => $otp, 'expires' => $expires, 'id' => $user['id']]);

                    // Send OTP via email
                    $to = $user['email'];
                    $subject = "Rocco Play Admin — Password Reset OTP";
                    $message = "Hello " . $user['name'] . ",\n\n";
                    $message .= "Your OTP for password reset is: " . $otp . "\n\n";
                    $message .= "This OTP is valid for 10 minutes.\n\n";
                    $message .= "If you did not request this, please ignore this email.\n\n";
                    $message .= "— Rocco Play Admin";
                    $headers = "From: noreply@roccoplay.com\r\n";
                    $headers .= "Reply-To: noreply@roccoplay.com\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();

                    @mail($to, $subject, $message, $headers);

                    // Store email in session for next step
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_user_id'] = $user['id'];
                    $step = 'otp';
                    $success = 'OTP has been sent to your email address.';
                } else {
                    $error = 'No account found with this email address.';
                }
            }
        }

        // ===== STEP 2: Verify OTP =====
        elseif ($action === 'verify_otp') {
            $otp_input = trim($_POST['otp'] ?? '');
            $user_id = $_SESSION['reset_user_id'] ?? null;

            if (empty($otp_input) || !$user_id) {
                $error = 'Please enter the OTP.';
                $step = 'otp';
            } else {
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = :id AND reset_otp = :otp AND reset_otp_expires > NOW()");
                $stmt->execute(['id' => $user_id, 'otp' => $otp_input]);
                $user = $stmt->fetch();

                if ($user) {
                    $_SESSION['otp_verified'] = true;
                    $step = 'reset';
                    $success = 'OTP verified successfully! Set your new password.';
                } else {
                    $error = 'Invalid or expired OTP. Please try again.';
                    $step = 'otp';
                }
            }
        }

        // ===== STEP 3: Reset Password =====
        elseif ($action === 'reset_password') {
            $user_id = $_SESSION['reset_user_id'] ?? null;
            $otp_verified = $_SESSION['otp_verified'] ?? false;
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (!$user_id || !$otp_verified) {
                $error = 'Session expired. Please start over.';
                $step = 'email';
            } elseif (empty($new_password) || strlen($new_password) < 6) {
                $error = 'Password must be at least 6 characters.';
                $step = 'reset';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Passwords do not match.';
                $step = 'reset';
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = :password, reset_otp = NULL, reset_otp_expires = NULL WHERE id = :id");
                $stmt->execute(['password' => $hashed, 'id' => $user_id]);

                // Clear session data
                unset($_SESSION['reset_email'], $_SESSION['reset_user_id'], $_SESSION['otp_verified']);

                $_SESSION['flash_login'] = 'Password reset successful! Please login with your new password.';
                header('Location: login.php');
                exit;
            }
        }
    }
}

// Restore step from session if coming back
if (isset($_SESSION['reset_user_id']) && $step === 'email' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $step = 'otp';
}
if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified'] && $step !== 'reset' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $step = 'reset';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — Rocco Play Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="icon" type="image/jpeg" href="../img/logo.jpg">
  <style>
    .otp-inputs {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin: 24px 0;
    }
    .otp-inputs input {
      width: 50px;
      height: 56px;
      text-align: center;
      font-size: 1.4rem;
      font-weight: 700;
      font-family: 'Outfit', sans-serif;
      background: var(--bg-input);
      border: 2px solid rgba(177, 18, 38, 0.3);
      border-radius: 12px;
      color: var(--text-primary);
      outline: none;
      transition: all 0.2s ease;
      caret-color: var(--brand-gold);
    }
    .otp-inputs input:focus {
      border-color: var(--brand-gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15), 0 0 20px rgba(212, 175, 55, 0.1);
      transform: translateY(-2px);
    }
    .otp-inputs input.filled {
      border-color: var(--brand-gold);
      background: rgba(212, 175, 55, 0.05);
    }
    .step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 28px;
    }
    .step-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255,255,255,0.15);
      transition: all 0.3s ease;
    }
    .step-dot.active {
      background: var(--brand-gold);
      box-shadow: 0 0 10px rgba(212,175,55,0.4);
    }
    .step-dot.done {
      background: #2ecc71;
      box-shadow: 0 0 10px rgba(46,204,113,0.3);
    }
    .step-line {
      width: 30px;
      height: 2px;
      background: rgba(255,255,255,0.1);
    }
    .success-msg {
      background: rgba(46, 204, 113, 0.1);
      border: 1px solid rgba(46, 204, 113, 0.2);
      border-radius: 10px;
      padding: 12px 16px;
      color: #2ecc71;
      font-size: 0.85rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .back-to-login {
      display: block;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.85rem;
      margin-top: 20px;
      transition: color 0.2s ease;
    }
    .back-to-login:hover {
      color: var(--brand-gold);
    }
    .resend-otp {
      text-align: center;
      margin-top: 12px;
    }
    .resend-otp button {
      background: none;
      border: none;
      color: var(--brand-gold);
      font-size: 0.82rem;
      cursor: pointer;
      font-family: var(--font-body);
      text-decoration: underline;
      transition: opacity 0.2s ease;
    }
    .resend-otp button:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      text-decoration: none;
    }
    .timer-text {
      font-size: 0.78rem;
      color: var(--text-muted);
      text-align: center;
      margin-top: 8px;
    }
  </style>
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <div class="login-logo">
        <img src="../img/logo.jpg" alt="Rocco Play">
        <h1>RoccoPlay</h1>
        <p>Reset Password</p>
      </div>

      <!-- Step Indicator -->
      <div class="step-indicator">
        <div class="step-dot <?php echo $step === 'email' ? 'active' : 'done'; ?>"></div>
        <div class="step-line"></div>
        <div class="step-dot <?php echo $step === 'otp' ? 'active' : ($step === 'reset' ? 'done' : ''); ?>"></div>
        <div class="step-line"></div>
        <div class="step-dot <?php echo $step === 'reset' ? 'active' : ''; ?>"></div>
      </div>

      <?php if ($error): ?>
      <div class="login-error show">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
      <?php endif; ?>

      <?php if ($success): ?>
      <div class="success-msg">
        <i class="fas fa-check-circle"></i>
        <span><?php echo htmlspecialchars($success); ?></span>
      </div>
      <?php endif; ?>

      <!-- ===== STEP 1: Enter Email ===== -->
      <?php if ($step === 'email'): ?>
      <form method="POST" action="forgot_password.php" id="emailForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="send_otp">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-icon-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" class="form-control-admin" placeholder="admin@roccoplay.com" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autocomplete="email">
          </div>
          <div class="form-hint" style="margin-top:8px;">We'll send a 6-digit OTP to your registered email.</div>
        </div>

        <button type="submit" class="login-btn">
          <i class="fas fa-paper-plane"></i> Send OTP
        </button>
      </form>
      <?php endif; ?>

      <!-- ===== STEP 2: Enter OTP ===== -->
      <?php if ($step === 'otp'): ?>
      <form method="POST" action="forgot_password.php" id="otpForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="verify_otp">
        <input type="hidden" name="otp" id="otpHidden" value="">

        <div style="text-align:center;margin-bottom:8px;">
          <p style="color:var(--text-secondary);font-size:0.88rem;">Enter the 6-digit code sent to</p>
          <p style="color:var(--brand-gold);font-weight:600;font-size:0.92rem;"><?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?></p>
        </div>

        <div class="otp-inputs" id="otpInputs">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="0" autofocus>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="1">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="2">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="3">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="4">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" data-index="5">
        </div>

        <button type="submit" class="login-btn" id="verifyBtn">
          <i class="fas fa-shield-check"></i> Verify OTP
        </button>

        <div class="timer-text" id="timerText">OTP expires in <span id="countdown">10:00</span></div>

        <div class="resend-otp">
          <button type="button" id="resendBtn" disabled onclick="resendOtp()">Resend OTP</button>
        </div>
      </form>
      <?php endif; ?>

      <!-- ===== STEP 3: New Password ===== -->
      <?php if ($step === 'reset'): ?>
      <form method="POST" action="forgot_password.php" id="resetForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="reset_password">

        <div class="form-group">
          <label class="form-label">New Password</label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="new_password" class="form-control-admin" placeholder="Enter new password" required minlength="6" autocomplete="new-password">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="confirm_password" class="form-control-admin" placeholder="Confirm new password" required minlength="6" autocomplete="new-password">
          </div>
        </div>

        <button type="submit" class="login-btn">
          <i class="fas fa-key"></i> Reset Password
        </button>
      </form>
      <?php endif; ?>

      <a href="login.php" class="back-to-login">
        <i class="fas fa-arrow-left"></i> Back to Login
      </a>
    </div>
  </div>

  <script>
  // ===== OTP Input Auto-Focus & Behaviour =====
  document.addEventListener('DOMContentLoaded', function() {
    var digits = document.querySelectorAll('.otp-digit');
    if (digits.length === 0) return;

    digits.forEach(function(input, idx) {
      input.addEventListener('input', function(e) {
        var val = this.value.replace(/\D/g, '');
        this.value = val;
        if (val && idx < 5) {
          digits[idx + 1].focus();
        }
        this.classList.toggle('filled', val.length > 0);
        updateOtpHidden();
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && idx > 0) {
          digits[idx - 1].focus();
          digits[idx - 1].value = '';
          digits[idx - 1].classList.remove('filled');
          updateOtpHidden();
        }
      });

      // Allow paste
      input.addEventListener('paste', function(e) {
        e.preventDefault();
        var paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 6);
        for (var i = 0; i < paste.length && i < 6; i++) {
          digits[i].value = paste[i];
          digits[i].classList.add('filled');
        }
        if (paste.length > 0) {
          digits[Math.min(paste.length, 5)].focus();
        }
        updateOtpHidden();
      });
    });

    function updateOtpHidden() {
      var otp = '';
      digits.forEach(function(d) { otp += d.value; });
      document.getElementById('otpHidden').value = otp;
    }

    // ===== Countdown Timer =====
    var countdownEl = document.getElementById('countdown');
    if (countdownEl) {
      var totalSeconds = 600; // 10 minutes
      var timer = setInterval(function() {
        totalSeconds--;
        var mins = Math.floor(totalSeconds / 60);
        var secs = totalSeconds % 60;
        countdownEl.textContent = mins + ':' + (secs < 10 ? '0' : '') + secs;
        if (totalSeconds <= 0) {
          clearInterval(timer);
          countdownEl.textContent = 'Expired';
          document.getElementById('resendBtn').disabled = false;
        }
      }, 1000);

      // Enable resend after 60 seconds
      setTimeout(function() {
        document.getElementById('resendBtn').disabled = false;
      }, 60000);
    }
  });

  function resendOtp() {
    // Create a form and submit to resend OTP
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'forgot_password.php';
    
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_token';
    csrf.value = '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>';
    form.appendChild(csrf);

    var action = document.createElement('input');
    action.type = 'hidden';
    action.name = 'action';
    action.value = 'send_otp';
    form.appendChild(action);

    var email = document.createElement('input');
    email.type = 'hidden';
    email.name = 'email';
    email.value = '<?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?>';
    form.appendChild(email);

    document.body.appendChild(form);
    form.submit();
  }
  </script>
</body>
</html>
