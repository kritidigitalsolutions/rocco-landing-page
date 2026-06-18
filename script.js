/* =============================================
   ROCCO PLAY — Main JavaScript
   GSAP + ScrollTrigger + Swiper + Lenis
   Full Power OTT Landing Page
   ============================================= */

// ─────────────────────────────────────────────
// 1. LENIS SMOOTH SCROLLING
// ─────────────────────────────────────────────
const lenis = new Lenis({
  duration: 1.4,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  orientation: 'vertical',
  gestureOrientation: 'vertical',
  smoothWheel: true,
  wheelMultiplier: 1,
  touchMultiplier: 2,
  infinite: false,
});

// Sync Lenis with GSAP ScrollTrigger
lenis.on('scroll', ScrollTrigger.update);

gsap.ticker.add((time) => {
  lenis.raf(time * 1000);
});

gsap.ticker.lagSmoothing(0);

// ─────────────────────────────────────────────
// 2. CUSTOM CURSOR
// ─────────────────────────────────────────────
// Clean URL by removing index.html (Works on Live Server / Hosting)
try {
  if (window.location.href.indexOf('index.html') > -1 && window.location.protocol !== 'file:') {
    const cleanUrl = window.location.href.replace('index.html', '');
    window.history.replaceState({}, document.title, cleanUrl);
  }
} catch (e) {
  console.log("URL cleanup skipped for local file.");
}

const cursor = document.getElementById('customCursor');
let mouseX = window.innerWidth / 2;
let mouseY = window.innerHeight / 2;
let cursorX = mouseX;
let cursorY = mouseY;

if (cursor && window.innerWidth > 640) {
  document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  gsap.ticker.add(() => {
    cursorX += (mouseX - cursorX) * 0.15; /* Adjust 0.15 for more/less smoothness */
    cursorY += (mouseY - cursorY) * 0.15;
    gsap.set(cursor, { x: cursorX, y: cursorY });
  });

  const interactiveElements = document.querySelectorAll(
    'a, button, .movie-card, .originals-card, .category-card, .plan-card, .app-badge, .feature-card, .testimonial-card'
  );

  interactiveElements.forEach((el) => {
    el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
    el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
  });
}

// ─────────────────────────────────────────────
// 3. AMBIENT PARTICLES
// ─────────────────────────────────────────────
function createParticles() {
  const container = document.getElementById('particlesBg');
  if (!container) return;

  const particleCount = 40;

  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.classList.add('particle');

    const size = Math.random() * 3 + 1;
    particle.style.width = `${size}px`;
    particle.style.height = `${size}px`;
    particle.style.left = `${Math.random() * 100}%`;
    particle.style.animationDuration = `${Math.random() * 15 + 10}s`;
    particle.style.animationDelay = `${Math.random() * 10}s`;
    particle.style.opacity = Math.random() * 0.12 + 0.03;

    container.appendChild(particle);
  }
}

createParticles();

// ─────────────────────────────────────────────
// 4. NAVBAR SCROLL EFFECT
// ─────────────────────────────────────────────
const navbar = document.getElementById('navbar');

ScrollTrigger.create({
  trigger: 'body',
  start: 'top -80',
  onEnter: () => navbar.classList.add('scrolled'),
  onLeaveBack: () => navbar.classList.remove('scrolled'),
});

// ─────────────────────────────────────────────
// 5. HERO BACKGROUND SLIDER — SMOOTH TRANSITIONS
// ─────────────────────────────────────────────
const heroSlides = document.querySelectorAll('.hero-bg-slide');
const indicatorsContainer = document.getElementById('heroIndicators');
let currentSlide = 0;
let heroInterval;

// Generate indicators dynamically
if (indicatorsContainer) {
  heroSlides.forEach((_, i) => {
    const ind = document.createElement('div');
    ind.classList.add('hero-indicator');
    if (i === 0) ind.classList.add('active');
    ind.dataset.index = i;
    indicatorsContainer.appendChild(ind);
  });
}

const heroIndicators = document.querySelectorAll('.hero-indicator');

function goToSlide(index) {
  // Fade out current
  heroSlides.forEach((slide, i) => {
    slide.classList.toggle('active', i === index);
  });
  heroIndicators.forEach((ind, i) => {
    ind.classList.toggle('active', i === index);
  });

  // Ken Burns effect on active slide
  const activeImg = heroSlides[index].querySelector('img');
  gsap.killTweensOf(activeImg);
  gsap.fromTo(activeImg,
    { scale: 1.08, x: 0 },
    {
      scale: 1.02,
      x: 0,
      duration: 8,
      ease: 'none'
    }
  );

  currentSlide = index;
}

function nextSlide() {
  const next = (currentSlide + 1) % heroSlides.length;
  goToSlide(next);
}

function startHeroSlider() {
  heroInterval = setInterval(nextSlide, 6000);
}

heroIndicators.forEach((ind) => {
  ind.addEventListener('click', () => {
    clearInterval(heroInterval);
    goToSlide(parseInt(ind.dataset.index));
    startHeroSlider();
  });
});

startHeroSlider();

// ─────────────────────────────────────────────
// 6. HERO ANIMATIONS (GSAP) — IMMEDIATE ON LOAD
// ─────────────────────────────────────────────
function initHeroAnimations() {
  const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

  tl.from('.hero-badge', {
    y: 30,
    opacity: 0,
    duration: 0.8,
  })
  .from('.hero-description', {
    y: 40,
    opacity: 0,
    duration: 0.8,
  }, '-=0.3')
  .from('.hero-meta-item', {
    y: 30,
    opacity: 0,
    duration: 0.6,
    stagger: 0.1,
  }, '-=0.3')
  .from('.hero-indicators', {
    opacity: 0,
    x: 30,
    duration: 0.6,
  }, '-=0.3')
  .from('.hero-scroll-indicator', {
    opacity: 0,
    y: 20,
    duration: 0.6,
  }, '-=0.2');
}

// Start hero animations immediately on load (no preloader)
window.addEventListener('load', () => {
  initHeroAnimations();
  initCountUp();
});

// ─────────────────────────────────────────────
// 7. COUNT UP ANIMATION FOR HERO STATS
// ─────────────────────────────────────────────
function initCountUp() {
  const counters = document.querySelectorAll('[data-count]');
  counters.forEach((el) => {
    const target = parseInt(el.dataset.count);
    gsap.to(el, {
      innerText: target,
      duration: 2.5,
      ease: 'power2.out',
      snap: { innerText: 1 },
      scrollTrigger: {
        trigger: el,
        start: 'top 90%',
        once: true,
      },
      onUpdate: function() {
        const val = Math.round(parseFloat(el.innerText));
        if (target >= 1000) {
          el.innerText = val.toLocaleString() + '+';
        } else {
          el.innerText = val + '+';
        }
      }
    });
  });
}

// ─────────────────────────────────────────────
// 8. SCROLL-TRIGGERED REVEAL ANIMATIONS
// ─────────────────────────────────────────────
function initScrollAnimations() {
  // Reveal-up elements
  gsap.utils.toArray('.reveal-up').forEach((el) => {
    gsap.to(el, {
      y: 0,
      opacity: 1,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
        toggleActions: 'play none none none',
      },
    });
  });

  // Reveal-scale (movie cards, category cards etc.)
  gsap.utils.toArray('.reveal-scale').forEach((el, i) => {
    gsap.to(el, {
      scale: 1,
      opacity: 1,
      duration: 0.7,
      delay: (i % 8) * 0.06,
      ease: 'back.out(1.4)',
      scrollTrigger: {
        trigger: el,
        start: 'top 92%',
        toggleActions: 'play none none none',
      },
    });
  });

  // Section headers — parallax title effect removed

  // Plans cards stagger — COMMENTED OUT
  /*
  gsap.utils.toArray('.plan-card').forEach((card, i) => {
    gsap.to(card, {
      y: 0,
      opacity: 1,
      duration: 0.8,
      delay: i * 0.15,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.plans-grid',
        start: 'top 80%',
        toggleActions: 'play none none none',
      },
    });
  });
  */

  // Feature cards stagger
  gsap.utils.toArray('.feature-card').forEach((card, i) => {
    gsap.to(card, {
      y: 0,
      opacity: 1,
      duration: 0.8,
      delay: i * 0.1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.features-grid',
        start: 'top 85%',
        toggleActions: 'play none none none',
      },
    });
  });

  // CTA section
  gsap.to('.cta-card', {
    y: 0,
    opacity: 1,
    duration: 1,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.cta-card',
      start: 'top 85%',
      toggleActions: 'play none none none',
    },
  });

  // Footer reveal
  gsap.from('.footer-grid > *', {
    y: 40,
    opacity: 0,
    duration: 0.7,
    stagger: 0.1,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.footer',
      start: 'top 85%',
      toggleActions: 'play none none none',
    },
  });

  // Stats ticker parallax
  gsap.to('.stats-ticker', {
    scrollTrigger: {
      trigger: '.stats-ticker',
      start: 'top bottom',
      end: 'bottom top',
      scrub: 1,
    },
  });
}

initScrollAnimations();

// ─────────────────────────────────────────────
// (Removed unused Originals Swiper)
// ─────────────────────────────────────────────

// ─────────────────────────────────────────────
// 10. SWIPER - 3D PHONE SLIDER
// ─────────────────────────────────────────────
const phoneSwiper = new Swiper('.phone-swiper', {
  effect: 'coverflow',
  grabCursor: true,
  centeredSlides: true,
  slidesPerView: 'auto',
  loop: true,
  coverflowEffect: {
    rotate: 20,
    stretch: 0,
    depth: 200,
    modifier: 1,
    slideShadows: true,
  },
  speed: 800,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
});

// ─────────────────────────────────────────────
// 11. SWIPER - TESTIMONIALS
// ─────────────────────────────────────────────
const testimonialsSwiper = new Swiper('.testimonials-swiper', {
  slidesPerView: 1,
  spaceBetween: 28,
  speed: 600,
  loop: true,
  grabCursor: true,
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
    pauseOnMouseEnter: true,
  },
  pagination: {
    el: '.testimonials-pagination',
    clickable: true,
  },
  breakpoints: {
    768: {
      slidesPerView: 2,
      spaceBetween: 24,
    },
  },
});

// ─────────────────────────────────────────────
// 12. CONTINUOUS AUTO-SLIDING CAROUSELS
// ─────────────────────────────────────────────
document.querySelectorAll('.carousel-wrapper').forEach((wrapper) => {
  const track = wrapper.querySelector('.carousel-track');
  const overlayBtns = wrapper.querySelectorAll('.carousel-nav');
  if (!track) return;

  // Hide old navigation buttons as it's auto-sliding
  overlayBtns.forEach(btn => btn.style.display = 'none');

  // Clone content for infinite loop
  const content = track.innerHTML;
  track.innerHTML += content;

  // Fix lazy load for cloned images so they don't stay invisible
  track.querySelectorAll('img').forEach(img => {
    if (img.complete) { img.classList.add('loaded'); }
    else { img.addEventListener('load', () => img.classList.add('loaded')); }
  });

  // Calculate duration based on number of items so speed is consistent
  const itemCount = track.querySelectorAll('.movie-card, .cat-card').length / 2;
  const duration = Math.max(itemCount * 3.5, 10); // Minimum 10s duration

  const tl = gsap.to(track, {
    xPercent: -50,
    ease: 'none',
    duration: duration,
    repeat: -1
  });

  wrapper.addEventListener('mouseenter', () => tl.pause());
  wrapper.addEventListener('mouseleave', () => tl.play());
});

// ─────────────────────────────────────────────
// 13. HAMBURGER MENU
// ─────────────────────────────────────────────
const hamburger = document.getElementById('hamburger');
const mobileNav = document.getElementById('mobileNav');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('active');
  mobileNav.classList.toggle('active');

  if (mobileNav.classList.contains('active')) {
    lenis.stop();
    // Animate mobile nav links
    gsap.from('.mobile-nav-link', {
      y: 30,
      opacity: 0,
      duration: 0.5,
      stagger: 0.08,
      ease: 'power3.out',
      delay: 0.2,
    });
  } else {
    lenis.start();
  }
});

document.querySelectorAll('.mobile-nav-link').forEach((link) => {
  link.addEventListener('click', () => {
    hamburger.classList.remove('active');
    mobileNav.classList.remove('active');
    lenis.start();
  });
});

// ─────────────────────────────────────────────
// 14. SCROLL TO TOP BUTTON
// ─────────────────────────────────────────────
const scrollTopBtn = document.getElementById('scrollTopBtn');

ScrollTrigger.create({
  trigger: 'body',
  start: 'top -400',
  onEnter: () => scrollTopBtn.classList.add('visible'),
  onLeaveBack: () => scrollTopBtn.classList.remove('visible'),
});

scrollTopBtn.addEventListener('click', () => {
  lenis.scrollTo(0, { duration: 1.5 });
});

// ─────────────────────────────────────────────
// 15. LAZY LOAD IMAGES
// ─────────────────────────────────────────────
function initLazyLoad() {
  const lazyImages = document.querySelectorAll('img[loading="lazy"]');

  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.classList.add('loaded');
          imageObserver.unobserve(img);
        }
      });
    }, {
      rootMargin: '100px',
    });

    lazyImages.forEach((img) => {
      if (img.complete) {
        img.classList.add('loaded');
      } else {
        img.addEventListener('load', () => img.classList.add('loaded'));
        imageObserver.observe(img);
      }
    });
  } else {
    lazyImages.forEach((img) => img.classList.add('loaded'));
  }
}

initLazyLoad();

// ─────────────────────────────────────────────
// 16. SMOOTH SCROLL FOR ANCHOR LINKS
// ─────────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', (e) => {
    const targetId = anchor.getAttribute('href');
    if (targetId === '#') return;

    e.preventDefault();
    const target = document.querySelector(targetId);
    if (target) {
      lenis.scrollTo(target, {
        offset: -80,
        duration: 1.2,
      });
    }
  });
});

// ─────────────────────────────────────────────
// 17. PARALLAX EFFECTS (GSAP)
// ─────────────────────────────────────────────

// Ambient glow parallax
gsap.utils.toArray('.ambient-glow').forEach((glow) => {
  gsap.to(glow, {
    y: -100,
    ease: 'none',
    scrollTrigger: {
      trigger: glow.parentElement,
      start: 'top bottom',
      end: 'bottom top',
      scrub: 1.5,
    },
  });
});

// Hero content parallax (fade out on scroll)
gsap.to('.hero-content', {
  y: -80,
  opacity: 0.3,
  ease: 'none',
  scrollTrigger: {
    trigger: '.hero',
    start: 'top top',
    end: 'bottom top',
    scrub: true,
  },
});

// Hero bg parallax zoom
gsap.to('.hero-bg', {
  scale: 1.1,
  ease: 'none',
  scrollTrigger: {
    trigger: '.hero',
    start: 'top top',
    end: 'bottom top',
    scrub: true,
  },
});

// Category cards tilt effect
gsap.utils.toArray('.category-card').forEach((card, i) => {
  gsap.to(card, {
    y: i % 2 === 0 ? -15 : 15,
    ease: 'none',
    scrollTrigger: {
      trigger: card,
      start: 'top bottom',
      end: 'bottom top',
      scrub: 2,
    },
  });
});

// Feature cards floating effect
gsap.utils.toArray('.feature-card').forEach((card, i) => {
  gsap.to(card, {
    y: i % 3 === 0 ? -10 : (i % 3 === 1 ? -20 : -5),
    ease: 'none',
    scrollTrigger: {
      trigger: card,
      start: 'top bottom',
      end: 'bottom top',
      scrub: 2,
    },
  });
});

// ─────────────────────────────────────────────
// 18. MOUSE GLOW TRACKING
// ─────────────────────────────────────────────
document.addEventListener('mousemove', (e) => {
  const x = e.clientX;
  const y = e.clientY;
  document.documentElement.style.setProperty('--mouse-x', `${x}px`);
  document.documentElement.style.setProperty('--mouse-y', `${y}px`);
});

// Removed touch support and hint as it auto-slides via GSAP.

// ─────────────────────────────────────────────
// 21. MAGNETIC BUTTON EFFECT
// ─────────────────────────────────────────────
document.querySelectorAll('.btn-primary, .btn-glow').forEach((btn) => {
  btn.addEventListener('mousemove', (e) => {
    const rect = btn.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;

    gsap.to(btn, {
      x: x * 0.15,
      y: y * 0.15,
      duration: 0.3,
      ease: 'power2.out',
    });
  });

  btn.addEventListener('mouseleave', () => {
    gsap.to(btn, {
      x: 0,
      y: 0,
      duration: 0.5,
      ease: 'elastic.out(1, 0.5)',
    });
  });
});

// ─────────────────────────────────────────────
// 22. SCROLL TRIGGER REFRESH ON RESIZE
// ─────────────────────────────────────────────
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    ScrollTrigger.refresh();
  }, 250);
});

// ─────────────────────────────────────────────
// 23. TEXT SCRAMBLE EFFECT REMOVED
// ─────────────────────────────────────────────

// ─────────────────────────────────────────────
// 24. GSAP SCROLLTRIGGER — SECTION PIN & SCALE
// ─────────────────────────────────────────────

// Scale down sections slightly as they scroll out for a cinematic feel
gsap.utils.toArray('section:not(.hero):not(.stats-ticker)').forEach((section) => {
  gsap.fromTo(section,
    { scale: 0.96, borderRadius: '24px', opacity: 0.8 },
    {
      scale: 1,
      borderRadius: '0px',
      opacity: 1,
      duration: 0.5,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: section,
        start: 'top 95%',
        end: 'top 60%',
        scrub: 1,
      },
    }
  );
});

// ─────────────────────────────────────────────
// DONE
// ─────────────────────────────────────────────
console.log('🎬 Rocco Play — Loaded Successfully | GSAP + Swiper + Lenis');
