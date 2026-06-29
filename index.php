<?php include 'header.php'; ?>
<?php
require_once __DIR__ . '/admin/includes/hero_slides_schema.php';
$defaultHeroSlides = getDefaultHeroSlides();

try {
    $heroSlides = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
    if (count($heroSlides) === 0) {
        $heroSlides = $defaultHeroSlides;
    }
} catch (Exception $e) {
    $heroSlides = $defaultHeroSlides;
}

$heroContent = $heroSlides[0];
?>
  <!-- ============ HERO SECTION ============ -->
  <section class="hero" id="hero">
    <div class="hero-bg">
      <div class="hero-bg-slider" id="heroBgSlider">
        <?php foreach ($heroSlides as $index => $slide): ?>
        <?php $heroImg = str_replace(' ', '%20', $slide['image_path']); ?>
        <div class="hero-bg-slide <?php echo $index === 0 ? 'active' : ''; ?>"
             data-badge-text="<?php echo htmlspecialchars($slide['badge_text'] ?? '', ENT_QUOTES); ?>"
             data-badge-icon="<?php echo htmlspecialchars($slide['badge_icon'] ?? 'fas fa-satellite-dish', ENT_QUOTES); ?>"
             data-heading="<?php echo htmlspecialchars($slide['heading'] ?? '', ENT_QUOTES); ?>"
             data-paragraph="<?php echo htmlspecialchars($slide['paragraph'] ?? '', ENT_QUOTES); ?>">
          <img src="<?php echo htmlspecialchars($heroImg); ?>" alt="<?php echo htmlspecialchars($slide['heading'] ?? 'Hero'); ?>" />
        </div>
        <?php endforeach; ?>
      </div>
      <div class="hero-overlay"></div>
      <div class="hero-vignette"></div>
    </div>

    <!-- Hero Content -->
    <div class="container hero-content">
      <div class="hero-badge">
        <span class="hero-badge-dot"></span>
        <i class="<?php echo htmlspecialchars($heroContent['badge_icon'] ?? 'fas fa-satellite-dish'); ?>"></i> <span id="heroBadgeText"><?php echo htmlspecialchars($heroContent['badge_text'] ?? ''); ?></span>
      </div>

      <h1 class="hero-title">
        <span id="heroTitleText"><?php echo htmlspecialchars($heroContent['heading'] ?? 'Unlimited Movies, Shows & Originals'); ?></span>
      </h1>

      <p class="hero-description">
        <span id="heroDescriptionText"><?php echo htmlspecialchars($heroContent['paragraph'] ?? ''); ?></span>
      </p>
    </div>

    <!-- Slide Indicators -->
    <div class="hero-indicators" id="heroIndicators"></div>

    <!-- Scroll Down -->
    <div class="hero-scroll-indicator">
      <div class="scroll-mouse"><div class="scroll-wheel"></div></div>
      <span>Scroll to explore</span>
    </div>
  </section>

  <!-- ============ LIVE STATS TICKER ============ -->
  <section class="stats-ticker" id="statsTicker">
    <div class="ticker-track">
      <div class="ticker-item"><i class="fas fa-fire"></i> <strong>2.5M</strong> Active Viewers Right Now</div>
      <div class="ticker-item"><i class="fas fa-trophy"></i> <strong>#1</strong> Streaming Platform 2026</div>
      <div class="ticker-item"><i class="fas fa-star"></i> <strong>4.9â˜…</strong> App Store Rating</div>
      <div class="ticker-item"><i class="fas fa-download"></i> <strong>10k+</strong> Downloads Worldwide</div>
      <div class="ticker-item"><i class="fas fa-film"></i> <strong>10000+</strong> New Titles This Month</div>
      <div class="ticker-item"><i class="fas fa-award"></i> <strong>35</strong> Award-Winning Originals</div>
      <div class="ticker-item"><i class="fas fa-headphones"></i> <strong>Best Sound</strong> Experiences</div>
      <div class="ticker-item"><i class="fas fa-shield-halved"></i> <strong>100%</strong> Ad-Free Experience</div>
      <!-- Duplicate for seamless loop -->
      <div class="ticker-item"><i class="fas fa-fire"></i> <strong>2.5M</strong> Active Viewers Right Now</div>
      <div class="ticker-item"><i class="fas fa-trophy"></i> <strong>#1</strong> Streaming Platform 2026</div>
      <div class="ticker-item"><i class="fas fa-star"></i> <strong>4.9â˜…</strong> App Store Rating</div>
      <div class="ticker-item"><i class="fas fa-download"></i> <strong>10k+</strong> Downloads Worldwide</div>
      <div class="ticker-item"><i class="fas fa-film"></i> <strong>100+</strong> New Titles This Month</div>
      <div class="ticker-item"><i class="fas fa-award"></i> <strong>35</strong> Award-Winning Originals</div>
      <div class="ticker-item"><i class="fas fa-headphones"></i> <strong>Best Sound</strong> Experiences</div>
      <div class="ticker-item"><i class="fas fa-shield-halved"></i> <strong>100%</strong> Ad-Free Experience</div>
    </div>
  </section>

  <!-- ============ STATS SECTION (NEW) ============ -->
  <section class="stats-section" id="stats">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-card reveal-up">
          <div class="stat-icon"><i class="fas fa-film"></i></div>
          <div class="stat-value" data-count="15000">0</div>
          <div class="stat-label">Movies & Shows</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width: 95%;"></div></div>
        </div>
        <div class="stat-card reveal-up">
          <div class="stat-icon"><i class="fas fa-tv"></i></div>
          <div class="stat-value">4K</div>
          <div class="stat-label">Ultra HD Quality</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width: 100%;"></div></div>
        </div>
        <div class="stat-card reveal-up">
          <div class="stat-icon"><i class="fas fa-users"></i></div>
          <div class="stat-value" data-count="50">0</div>
          <div class="stat-label">Thousands+ Users</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width: 80%;"></div></div>
        </div>
        <div class="stat-card reveal-up">
          <div class="stat-icon"><i class="fas fa-globe"></i></div>
          <div class="stat-value" data-count="15">0</div>
          <div class="stat-label">Countries</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width: 90%;"></div></div>
        </div>
      </div>
    </div>




  <!-- ============ DYNAMIC MEDIA SLIDERS ============ -->
  <?php
  $categoriesList = $pdo->query("SELECT * FROM categories WHERE is_hidden=0 ORDER BY display_order ASC")->fetchAll();
  foreach ($categoriesList as $cat) {

      $stmt = $pdo->prepare("SELECT * FROM media WHERE category_id = ? ORDER BY display_order ASC");
      $stmt->execute([$cat['id']]);
      $medias = $stmt->fetchAll();
      if(count($medias) == 0) continue;
  ?>
  <section class="trending-section <?php echo $cat['is_originals'] ? 'originals-section' : ''; ?>" <?php echo $cat['is_originals'] ? 'id="originals"' : ''; ?>>
    <div class="container">
      <div class="ambient-glow <?php echo htmlspecialchars($cat['glow_color']); ?>" style="top: -200px; right: -200px;"></div>
      <div class="section-header">
        <div>
          <div class="section-label"><i class="<?php echo htmlspecialchars($cat['icon']); ?>"></i> <?php echo htmlspecialchars($cat['section_label']); ?></div>
          <h2 class="section-title"><?php echo htmlspecialchars($cat['name']); ?></h2>
        </div>
        <a href="#" class="section-see-all">See All <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="carousel-wrapper">
        <div class="carousel-track" id="catTrack_<?php echo $cat['id']; ?>">
          <?php foreach ($medias as $index => $media) { ?>
          <div class="movie-card">
            <?php if($cat['is_originals']) { 
                if($media['badge_text']) {
                     $bClass = $media['badge_class'] ? $media['badge_class'] : '';
                     echo '<span class="originals-badge '.$bClass.'">'.htmlspecialchars($media['badge_text']).'</span>';
                }
            } else { ?>
                <span class="trending-number"><?php echo $index + 1; ?></span>
            <?php } ?>
            
            <?php 
               $imgSrc = $media['file_path'] ?: $media['img_path']; 
               // Encode spaces to %20 to ensure it loads in all browsers
               $imgSrc = str_replace(' ', '%20', $imgSrc);
            ?>
            <img class="movie-card-img" loading="lazy" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($media['title']); ?>" />
            <div class="movie-card-overlay">
              <div class="movie-card-title"><?php echo htmlspecialchars($media['title']); ?></div>
              <div class="movie-card-meta">
                <span class="movie-card-rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($media['rating']); ?></span>
                <span><?php echo htmlspecialchars($media['year_or_seasons']); ?></span>
                <span><?php echo htmlspecialchars($media['genre']); ?></span>
              </div>
            </div>
            <div class="movie-card-play"><i class="fas fa-play"></i></div>
          </div>
          <?php } ?>
        </div>
        <button class="carousel-nav prev" data-track="catTrack_<?php echo $cat['id']; ?>" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
        <button class="carousel-nav next" data-track="catTrack_<?php echo $cat['id']; ?>" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </section>
  <?php } ?>

  <!-- ============ POPULAR CATEGORIES — HORIZONTAL SCROLL CARDS ============ -->
  <?php
  $popCats = $pdo->query("SELECT * FROM popular_categories WHERE is_hidden=0 ORDER BY display_order ASC")->fetchAll();
  if (count($popCats) > 0) {
  ?>
  <section class="categories-section" id="categories">
    <div class="container">
      <div class="ambient-glow glow-red" style="bottom: -200px; right: -150px;"></div>
      <div class="section-header">
        <div>
          <div class="section-label"><i class="fas fa-star"></i> Browse</div>
          <h2 class="section-title">Popular Categories</h2>
        </div>
      </div>

      <div class="categories-scroll">
        <?php foreach ($popCats as $pcat): ?>
        <div class="cat-card reveal-scale" style="--cat-color: <?php echo htmlspecialchars($pcat['card_color']); ?>; <?php if($pcat['bg_image']) echo 'background-image:linear-gradient(to top, rgba(15,15,15,0.9), rgba(15,15,15,0.7)), url('.htmlspecialchars($pcat['bg_image']).');background-size:cover;background-position:center;'; ?>">
          <?php if(!$pcat['is_icon_hidden']): ?>
          <div class="cat-icon">
            <?php if(!empty($pcat['icon_class'])): ?>
              <i class="<?php echo htmlspecialchars($pcat['icon_class']); ?>"></i>
            <?php elseif($pcat['icon_image']): ?>
              <img src="<?php echo htmlspecialchars($pcat['icon_image']); ?>" alt="icon" style="height:40px;width:auto;object-fit:contain;margin-bottom:12px;">
            <?php else: ?>
              <i class="fas fa-star"></i>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <h3><?php echo htmlspecialchars($pcat['name']); ?></h3>
          <span><?php echo htmlspecialchars($pcat['titles_count']); ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php } ?>

  <!-- ============ WHY CHOOSE US ============ -->
  <section class="features-section" id="features">
    <div class="container">
      <div class="section-header">
        <div>
          <div class="section-label"><i class="fas fa-wand-magic-sparkles"></i> Why Rocco Play?</div>
          <h2 class="section-title">The Ultimate Streaming Experience</h2>
        </div>
      </div>
      <div class="features-grid">
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-display"></i></div><h3>4K Ultra HD & HDR</h3><p>Crystal clear picture quality with Dolby Vision HDR on all premium content.</p></div>
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-headphones-simple"></i></div><h3>Best Sound Experiences</h3><p>Immersive spatial audio that puts you right in the middle of the action.</p></div>
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-cloud-arrow-down"></i></div><h3>Offline Downloads</h3><p>Download your favorites and watch them anywhere without internet.</p></div>
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-layer-group"></i></div><h3>All in One Hub</h3><p>Access and stream your favorite content from other popular OTT platforms seamlessly.</p></div>
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-ban"></i></div><h3>Zero Ads</h3><p>Uninterrupted streaming with absolutely no advertisements.</p></div>
        <div class="feature-card reveal-up"><div class="feature-icon-wrap"><i class="fas fa-language"></i></div><h3>Multiple Language Support</h3><p>Audio & subtitles in Hindi, English, Tamil, Telugu, and more.</p></div>
      </div>
    </div>
  </section>

  <!-- ============ PLANS â€” COMMENTED OUT ============ -->
  <!--
  <section class="plans-section" id="plans">
    <div class="container">
      <div class="plans-header reveal-up">
        <div class="section-label"><i class="fas fa-crown"></i> Choose Your Plan</div>
        <h2 class="section-title">Unlock the Full Experience</h2>
        <p>Start streaming today with our flexible plans. No hidden fees, cancel anytime.</p>
      </div>
      <div class="plans-grid">
        <div class="plan-card reveal-up">
          <div class="plan-icon"><i class="fas fa-ticket"></i></div>
          <div class="plan-name">Basic</div>
          <div class="plan-price">â‚¹149<span>/mo</span></div>
          <div class="plan-tagline">Perfect for getting started</div>
          <ul class="plan-features">
            <li><span class="check"><i class="fas fa-check-circle"></i></span> 720p HD Streaming</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Watch on 1 Device</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Limited Catalog</li>
            <li><span class="cross"><i class="fas fa-times-circle"></i></span> No Downloads</li>
            <li><span class="cross"><i class="fas fa-times-circle"></i></span> No Originals</li>
          </ul>
          <a href="#" class="btn btn-outline plan-btn">Get Started</a>
        </div>
        <div class="plan-card featured reveal-up">
          <div class="plan-popular-badge"><i class="fas fa-fire"></i> Most Popular</div>
          <div class="plan-icon"><i class="fas fa-bolt"></i></div>
          <div class="plan-name">Pro</div>
          <div class="plan-price">â‚¹399<span>/mo</span></div>
          <div class="plan-tagline">Best value for entertainment</div>
          <ul class="plan-features">
            <li><span class="check"><i class="fas fa-check-circle"></i></span> 1080p Full HD</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Watch on 3 Devices</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Full Catalog Access</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Offline Downloads</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Rocco Originals</li>
          </ul>
          <a href="#" class="btn btn-primary plan-btn btn-glow">Get Pro</a>
        </div>
        <div class="plan-card reveal-up">
          <div class="plan-icon"><i class="fas fa-gem"></i></div>
          <div class="plan-name">Premium</div>
          <div class="plan-price">â‚¹699<span>/mo</span></div>
          <div class="plan-tagline">Ultimate cinematic experience</div>
          <ul class="plan-features">
            <li><span class="check"><i class="fas fa-check-circle"></i></span> 4K Ultra HD + HDR</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Watch on 5 Devices</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Full Catalog + Early Access</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Unlimited Downloads</li>
            <li><span class="check"><i class="fas fa-check-circle"></i></span> Best Sound Experiences</li>
          </ul>
          <a href="#" class="btn btn-outline plan-btn">Go Premium</a>
        </div>
      </div>
    </div>
  </section>
  -->

  <!-- ============ TESTIMONIALS ============ -->
  <section class="testimonials-section" id="testimonials">
    <div class="container">
      <div class="section-header text-center-header">
        <div style="display: flex; flex-direction: column; align-items: center;">
          <div class="section-label"><i class="fas fa-quote-left"></i> Testimonials</div>
          <h2 class="section-title">What Our Users Say</h2>
        </div>
      </div>
      <div class="swiper testimonials-swiper reveal-up">
        <div class="swiper-wrapper">
          <div class="swiper-slide"><div class="testimonial-card"><div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><p>"Rocco Play has completely replaced every other streaming service for me. The 4K quality is insane!"</p><div class="testimonial-author"><div class="testimonial-avatar">RP</div><div><strong>Rahul Patel</strong><span>Pro Subscriber</span></div></div></div></div>
          <div class="swiper-slide"><div class="testimonial-card"><div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><p>"The offline download feature is a lifesaver for my commute. And zero ads? Best decision ever."</p><div class="testimonial-author"><div class="testimonial-avatar">SM</div><div><strong>Sneha Mishra</strong><span>Premium Member</span></div></div></div></div>
          <div class="swiper-slide"><div class="testimonial-card"><div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div><p>"From Bollywood to Hollywood, they have everything. The best sound experiences are just incredible."</p><div class="testimonial-author"><div class="testimonial-avatar">AK</div><div><strong>Arjun Kumar</strong><span>Premium Subscriber</span></div></div></div></div>
          <div class="swiper-slide"><div class="testimonial-card"><div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div><p>"My whole family uses Rocco Play. The multiple profiles mean everyone gets their personalized experience!"</p><div class="testimonial-author"><div class="testimonial-avatar">PS</div><div><strong>Priya Sharma</strong><span>Family Plan</span></div></div></div></div>
        </div>
        <div class="swiper-pagination testimonials-pagination"></div>
      </div>
    </div>
  </section>

  <!-- ============ 3D APP SLIDER SECTION (NEW) ============ -->
  <section class="app-showcase-section" id="appShowcase">
    <div class="container">
      <div class="section-header text-center-header">
        <div style="display: flex; flex-direction: column; align-items: center;">
          <div class="section-label"><i class="fas fa-mobile-screen-button"></i> App Preview</div>
          <h2 class="section-title">Experience Rocco Play App</h2>
        </div>
      </div>

      <div class="phone-3d-showcase reveal-up">
        <div class="swiper phone-swiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/Home.jpg" alt="Home Screen" /></div></div>
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/Search.jpg" alt="Search Screen" /></div></div>
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/seen video.jpg" alt="Video Player" /></div></div>
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/Home.jpg" alt="Home Screen" /></div></div>
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/Search.jpg" alt="Search Screen" /></div></div>
            <div class="swiper-slide"><div class="phone-mockup"><img src="img/seen video.jpg" alt="Video Player" /></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ DOWNLOAD APP SECTION ============ -->
  <section class="cta-section" id="download">
    <div class="container">
      <div class="cta-card reveal-up">
        <div class="cta-content-center">
          <div class="section-label"><i class="fas fa-cloud-arrow-down"></i> Download Now</div>
          <h2 class="section-title">Take Rocco Play<br/>Everywhere You Go</h2>
          <p>Download the Rocco Play app and enjoy your favourite movies & series on the go. Available on iOS and Android.</p>
          <div class="cta-app-badges">
            <div class="app-badge">
              <div class="app-badge-icon"><i class="fab fa-apple"></i></div>
              <div class="app-badge-text">
                <small>Download on the</small>
                <strong>App Store</strong>
              </div>
            </div>
            <div class="app-badge">
              <div class="app-badge-icon"><i class="fab fa-google-play"></i></div>
              <div class="app-badge-text">
                <small>Get it on</small>
                <strong>Google Play</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php include 'footer.php'; ?>
