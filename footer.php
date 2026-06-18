  <!-- ============ FOOTER ============ -->
  <footer class="footer" id="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="index.php" class="nav-logo">
            <img src="img/logo.jpg" alt="Rocco Play" />
            <span class="nav-logo-text">RoccoPlay</span>
          </a>
          <p>Your ultimate entertainment destination. Stream unlimited movies, exclusive originals, and binge-worthy series in stunning quality.</p>

        </div>
        <div class="footer-col">
          <h4><i class="fas fa-link"></i> Quick Links</h4>
          <ul>
            <li><a href="about.php">About Us</a></li>
            <li><a href="index.php#trending">Trending</a></li>
            <li><a href="index.php#originals">Originals</a></li>
            <li><a href="index.php#download">Download App</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4><i class="fas fa-headset"></i> Support</h4>
          <ul>
            <li><a href="contact.php">Contact Us</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4><i class="fas fa-scale-balanced"></i> Legal</h4>
          <ul>
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <li><a href="terms-and-conditions.php">Terms &amp; Conditions</a></li>
            <li><a href="refund-policy.php">Refund Policy</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p><?php echo $siteSettings['copyright_text'] ?? ($global_settings['footer_copyright'] ?? '&copy; ' . date('Y') . ' RoccoPlay. All rights reserved.'); ?></p>
        <div class="footer-bottom-links">
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="terms-and-conditions.php">Terms &amp; Conditions</a>
          <a href="refund-policy.php">Refund Policy</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll to Top -->
  <button class="scroll-top" id="scrollTopBtn" aria-label="Scroll to top"><i class="fas fa-arrow-up"></i></button>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
