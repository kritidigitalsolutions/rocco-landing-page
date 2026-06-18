<?php
require_once __DIR__ . '/admin/includes/db.php';
$msgStatus = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $msgStatus = 'success';
        } else {
            $msgStatus = 'error';
        }
    }
}
include 'header.php'; 
?>
<style>
.legal-page { padding: 120px 20px 60px; max-width: 900px; margin: 0 auto; color: #fff; font-family: 'Inter', sans-serif; }
.legal-page h1 { font-size: 2.5rem; margin-bottom: 2rem; color: var(--brand-red, #B11226); text-align: center; }
.legal-page p { margin-bottom: 1.2rem; line-height: 1.7; color: rgba(255,255,255,0.85); font-size: 1.05rem; text-align: center; }
.contact-wrapper { display: flex; flex-wrap: wrap; gap: 40px; margin-top: 40px; }
.contact-info, .contact-form-container { flex: 1 1 400px; }
.contact-info h3 { color: var(--brand-gold, #D4AF37); margin-bottom: 20px; font-size: 1.5rem; }
.contact-detail { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; font-size: 1.1rem; }
.contact-detail i { font-size: 1.5rem; color: var(--brand-red, #B11226); width: 30px; text-align: center; }
.contact-form { display: flex; flex-direction: column; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label { font-size: 0.95rem; color: rgba(255,255,255,0.8); }
.form-group input, .form-group textarea { padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; font-family: 'Inter', sans-serif; font-size: 1rem; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--brand-red, #B11226); background: rgba(255,255,255,0.1); }
.submit-btn { padding: 15px 30px; background: var(--brand-red, #B11226); color: #fff; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
.submit-btn:hover { background: #E63946; box-shadow: 0 0 15px rgba(226, 57, 70, 0.5); }
</style>

<div class="legal-page">
    <h1>Contact Us</h1>
    <p>We're here to help! Whether you have a question about your subscription, need technical support, or just want to share feedback, please reach out to us.</p>
    
    <div class="contact-wrapper">
        <div class="contact-info">
            <h3>Get In Touch</h3>
            <div class="contact-detail">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>Email Support:</strong><br/>
                    <a href="mailto:support@roccoplay.in" style="color: #D4AF37; text-decoration: none;">support@roccoplay.in</a>
                </div>
            </div>
            
            <!-- Additional contact methods can be added here if needed -->
            <div class="contact-detail">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Phone Support:</strong><br/>
                    Available 9 AM - 6 PM (Mon-Fri)
                </div>
            </div>


        </div>

        <div class="contact-form-container">
            <h3>Send us a Message</h3>
            
            <?php if ($msgStatus === 'success'): ?>
                <div style="background: rgba(46, 204, 113, 0.15); color: #2ecc71; padding: 15px; border-radius: 8px; border: 1px solid rgba(46, 204, 113, 0.3); margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="margin-right: 10px;"></i> Your message has been sent successfully. We will get back to you soon.
                </div>
            <?php elseif ($msgStatus === 'error'): ?>
                <div style="background: rgba(177, 18, 38, 0.15); color: #E63946; padding: 15px; border-radius: 8px; border: 1px solid rgba(177, 18, 38, 0.3); margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> There was an error sending your message. Please try again.
                </div>
            <?php endif; ?>

            <form class="contact-form" action="contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="How can we help you?" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message <i class="fas fa-paper-plane" style="margin-left: 5px;"></i></button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
