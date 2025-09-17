(() => {
  function init() {
    const header = document.querySelector('.header');
    if (!header) return;

    // ===== Mobile menu toggle =====
    const nav = header.querySelector('.navbar');
    const menuBtn = document.getElementById('menu-btn');

    if (nav && menuBtn) {
      const syncAria = () => {
        const open = nav.classList.contains('active');
        menuBtn.setAttribute('aria-expanded', String(open));
        nav.setAttribute('aria-hidden', String(!open));
      };
      syncAria();

      const toggle = () => { nav.classList.toggle('active'); syncAria(); };
      const close = () => { nav.classList.remove('active'); syncAria(); };

      // Avoid duplicate handlers if page restored from cache
      menuBtn.onclick = null;
      menuBtn.addEventListener('click', toggle, { passive: true });
      window.addEventListener('scroll', close, { passive: true });
      nav.querySelectorAll('a').forEach(a => a.addEventListener('click', close, { passive: true }));
    }

    // ===== Login modal (only if present on the page) =====
    const loginBtn = document.getElementById('login-btn');
    const overlay = document.querySelector('.login-overlay');
    const modal = document.querySelector('.login-form');
    const closeBtn = document.getElementById('close-login-btn');
    const regLink = document.getElementById('register-link');
    const logLink = document.getElementById('login-link');

    if (loginBtn && overlay && modal) {
      const openLogin = () => { overlay.classList.add('active'); modal.classList.add('active'); };
      const closeLogin = () => { overlay.classList.remove('active'); modal.classList.remove('active'); };

      loginBtn.onclick = null;
      loginBtn.addEventListener('click', openLogin);
      overlay.addEventListener('click', closeLogin);
      if (closeBtn) closeBtn.addEventListener('click', closeLogin);

      // swap sections (register/login)
      if (regLink) {
        regLink.addEventListener('click', (e) => {
          e.preventDefault();
          const loginSection = modal.querySelector('.login-section');
          const registerSection = modal.querySelector('.register-section');
          if (loginSection && registerSection) {
            loginSection.style.display = 'none';
            registerSection.style.display = 'block';
          }
        });
      }
      if (logLink) {
        logLink.addEventListener('click', (e) => {
          e.preventDefault();
          const loginSection = modal.querySelector('.login-section');
          const registerSection = modal.querySelector('.register-section');
          if (loginSection && registerSection) {
            registerSection.style.display = 'none';
            loginSection.style.display = 'block';
          }
        });
      }

      // ESC to close
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') overlay.classList.contains('active') && closeLogin(); });
    }
  }

  // Run now or after DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }

  // Re-init when coming back via browser back/forward cache (bfcache)
  window.addEventListener('pageshow', (e) => { if (e.persisted) init(); });
})();

// Register password match (only if those fields exist on the page)
const registerBtn = document.getElementById('register-btn');
const signupPassword = document.getElementById('signup-password');
const confirmPassword = document.getElementById('confirm-password');

if (registerBtn && signupPassword && confirmPassword) {
  registerBtn.addEventListener('click', (e) => {
    if (signupPassword.value !== confirmPassword.value) {
      e.preventDefault();
      alert('Passwords do not match!');
    } else {
      alert('Registration successful!');
    }
  });
}
