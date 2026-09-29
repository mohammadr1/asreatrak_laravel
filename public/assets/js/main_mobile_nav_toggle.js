const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');

if (navToggle && navLinks) {
  const icon = navToggle.querySelector('i');

  function setMenuState(isOpen) {
    navLinks.classList.toggle('open', isOpen);
    navToggle.setAttribute('aria-expanded', String(isOpen));

    if (icon) {
      icon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
    }
  }

  navToggle.addEventListener('click', function () {
    setMenuState(!navLinks.classList.contains('open'));
  });

  navLinks.addEventListener('click', function (event) {
    if (event.target.closest('a') && window.matchMedia('(max-width: 991px)').matches) {
      setMenuState(false);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && navLinks.classList.contains('open')) {
      setMenuState(false);
      navToggle.focus();
    }
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 991) {
      setMenuState(false);
    }
  });
}
