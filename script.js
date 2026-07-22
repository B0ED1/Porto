// Portfolio Interactive Logic - M Arif Budi Prakoso (Calm Tone Theme)

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initProjectFilter();
  initLightbox();
  initScrollObserver();
  initMobileMenu();
  initLiveClock();
});

/* ----------------------------------------------------
   1. Dark / Light Theme Switcher (Calm Tone Default)
---------------------------------------------------- */
function initTheme() {
  const themeToggleBtn = document.getElementById('themeToggleBtn');
  
  // Default to 'light' (Calm Warm Ivory/Sand Tone)
  let storedTheme = localStorage.getItem('porto_theme');
  if (storedTheme !== 'dark' && storedTheme !== 'light') {
    storedTheme = 'light';
  }
  
  document.documentElement.setAttribute('data-theme', storedTheme);
  updateThemeIcon(storedTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('porto_theme', newTheme);
      updateThemeIcon(newTheme);
    });
  }
}

function updateThemeIcon(theme) {
  const themeIcon = document.getElementById('themeIcon');
  if (!themeIcon) return;
  
  if (theme === 'light') {
    // Moon icon for switching to calm dark
    themeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />`;
  } else {
    // Sun icon for switching to calm light
    themeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />`;
  }
}

/* ----------------------------------------------------
   2. Project Category Filtering
---------------------------------------------------- */
function initProjectFilter() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const projectCards = document.querySelectorAll('.project-card');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // Remove active state from all buttons
      filterBtns.forEach(b => {
        b.classList.remove('bg-gradient-calm', 'text-white', 'shadow-md');
        b.classList.add('glass-pill', 'text-sub');
      });

      // Add active state to clicked button
      btn.classList.remove('glass-pill', 'text-sub');
      btn.classList.add('bg-gradient-calm', 'text-white', 'shadow-md');

      const filterValue = btn.getAttribute('data-filter');

      projectCards.forEach(card => {
        const category = card.getAttribute('data-category');
        
        if (filterValue === 'all' || category.includes(filterValue)) {
          card.classList.remove('hide');
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
          }, 50);
        } else {
          card.style.opacity = '0';
          card.style.transform = 'scale(0.95)';
          setTimeout(() => {
            card.classList.add('hide');
          }, 300);
        }
      });
    });
  });
}

/* ----------------------------------------------------
   3. Photography Lightbox Modal
---------------------------------------------------- */
const galleryData = [
  {
    src: 'assets/gallery/photo1.png',
    title: 'Misty Alpine Peaks',
    location: 'Mount Merbabu, Central Java',
    category: 'Landscape',
    caption: 'Cinematic sunset vista overlooking golden hour clouds and rugged mountain ridge.'
  },
  {
    src: 'assets/gallery/photo2.png',
    title: 'Cyber Twilight Metropolis',
    location: 'Downtown Skyscraper District',
    category: 'Cityscape',
    caption: 'Neon reflections and sleek urban geometry captured during twilight hour.'
  },
  {
    src: 'assets/gallery/photo3.png',
    title: 'Lantern Alleyway',
    location: 'Historical Old Town District',
    category: 'Street',
    caption: 'Candid nocturnal street life lit by warm lanterns and dramatic ambient shadows.'
  },
  {
    src: 'assets/gallery/photo4.png',
    title: 'Monolithic Curves',
    location: 'Contemporary Art Center',
    category: 'Architecture',
    caption: 'Minimalist perspective exploring leading lines and glass facade reflections.'
  }
];

let currentImageIndex = 0;

function initLightbox() {
  const lightboxModal = document.getElementById('lightboxModal');
  const closeBtn = document.getElementById('lightboxClose');
  const prevBtn = document.getElementById('lightboxPrev');
  const nextBtn = document.getElementById('lightboxNext');

  const galleryItems = document.querySelectorAll('.gallery-item');

  galleryItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      openLightbox(index);
    });
  });

  if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
  if (prevBtn) prevBtn.addEventListener('click', showPrevImage);
  if (nextBtn) nextBtn.addEventListener('click', showNextImage);

  // Close modal on background click
  if (lightboxModal) {
    lightboxModal.addEventListener('click', (e) => {
      if (e.target === lightboxModal) closeLightbox();
    });
  }

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (!lightboxModal || !lightboxModal.classList.contains('active')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') showPrevImage();
    if (e.key === 'ArrowRight') showNextImage();
  });
}

function openLightbox(index) {
  currentImageIndex = index;
  updateLightboxContent();
  const lightboxModal = document.getElementById('lightboxModal');
  if (lightboxModal) {
    lightboxModal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeLightbox() {
  const lightboxModal = document.getElementById('lightboxModal');
  if (lightboxModal) {
    lightboxModal.classList.remove('active');
    document.body.style.overflow = 'auto';
  }
}

function showPrevImage() {
  currentImageIndex = (currentImageIndex - 1 + galleryData.length) % galleryData.length;
  updateLightboxContent();
}

function showNextImage() {
  currentImageIndex = (currentImageIndex + 1) % galleryData.length;
  updateLightboxContent();
}

function updateLightboxContent() {
  const item = galleryData[currentImageIndex];
  const lightboxImg = document.getElementById('lightboxImg');
  const lightboxTitle = document.getElementById('lightboxTitle');
  const lightboxMeta = document.getElementById('lightboxMeta');
  const lightboxCaption = document.getElementById('lightboxCaption');

  if (lightboxImg) lightboxImg.src = item.src;
  if (lightboxTitle) lightboxTitle.textContent = item.title;
  if (lightboxMeta) lightboxMeta.textContent = `${item.category} • ${item.location}`;
  if (lightboxCaption) lightboxCaption.textContent = item.caption;
}

/* ----------------------------------------------------
   4. Copy Email & Toast Notification
---------------------------------------------------- */
function copyEmail() {
  const emailText = 'budiarif396@gmail.com';
  navigator.clipboard.writeText(emailText).then(() => {
    showToast('Copied email to clipboard!');
  }).catch(err => {
    showToast('Email: budiarif396@gmail.com');
  });
}

function showToast(message) {
  const toast = document.getElementById('toast');
  const toastMsg = document.getElementById('toastMsg');
  if (!toast || !toastMsg) return;

  toastMsg.textContent = message;
  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

/* ----------------------------------------------------
   5. Mobile Navigation Drawer Toggle
---------------------------------------------------- */
function initMobileMenu() {
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    mobileNavLinks.forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
      });
    });
  }
}

/* ----------------------------------------------------
   6. Scroll Observer for Nav Highlighting
---------------------------------------------------- */
function initScrollObserver() {
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  window.addEventListener('scroll', () => {
    let current = '';
    const scrollY = window.pageYOffset;

    sections.forEach(section => {
      const sectionHeight = section.offsetHeight;
      const sectionTop = section.offsetTop - 120;
      const sectionId = section.getAttribute('id');

      if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
        current = sectionId;
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  });
}

/* ----------------------------------------------------
   7. Live Clock in Footer
---------------------------------------------------- */
function initLiveClock() {
  const clockEl = document.getElementById('liveClock');
  if (!clockEl) return;

  function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    clockEl.textContent = `${timeStr} (${timeZone})`;
  }

  updateClock();
  setInterval(updateClock, 1000);
}
