
/* ========================
   HERO SLIDER
======================== */
document.addEventListener('DOMContentLoaded', function () {
  let currentSlide = 0;
  const slider = document.getElementById('heroSlider');
  const slides = Array.from(document.querySelectorAll('.hero-slide'));
  const dots = Array.from(document.querySelectorAll('.slider-dot'));
  const nextButton = document.getElementById('nextSlide');
  const previousButton = document.getElementById('prevSlide');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let sliderTimer = null;

  if (!slider || slides.length === 0) {
    return;
  }

  function showSlide(idx) {
    const nextSlide = (idx + slides.length) % slides.length;
    const previousSlide = currentSlide;
    const direction = nextSlide >= previousSlide ? 'next' : 'previous';

    slides.forEach((slide) => {
      slide.classList.remove('active', 'slide-enter-next', 'slide-enter-previous');
      slide.setAttribute('aria-hidden', 'true');
    });
    dots.forEach((dot) => {
      dot.classList.remove('active');
      dot.removeAttribute('aria-current');
    });

    currentSlide = nextSlide;
    const activeSlide = slides[currentSlide];
    if (previousSlide !== currentSlide && !prefersReducedMotion.matches) {
      activeSlide.classList.add(direction === 'next' ? 'slide-enter-next' : 'slide-enter-previous');
    }
    activeSlide.classList.add('active');
    activeSlide.setAttribute('aria-hidden', 'false');

    if (dots[currentSlide]) {
      dots[currentSlide].classList.add('active');
      dots[currentSlide].setAttribute('aria-current', 'true');
    }
  }

  function goToSlide(idx) {
    showSlide(idx);
    resetTimer();
  }

  function stopTimer() {
    if (sliderTimer !== null) {
      window.clearInterval(sliderTimer);
      sliderTimer = null;
    }
  }

  function resetTimer() {
    stopTimer();

    if (prefersReducedMotion.matches || slides.length < 2) {
      return;
    }

    sliderTimer = window.setInterval(() => showSlide(currentSlide + 1), 5000);
  }

  window.goToSlide = goToSlide;

  nextButton?.addEventListener('click', () => {
    showSlide(currentSlide + 1);
    resetTimer();
  });

  previousButton?.addEventListener('click', () => {
    showSlide(currentSlide - 1);
    resetTimer();
  });

  slider.addEventListener('mouseenter', stopTimer);
  slider.addEventListener('mouseleave', resetTimer);
  slider.addEventListener('focusin', stopTimer);
  slider.addEventListener('focusout', resetTimer);

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      stopTimer();
    } else {
      resetTimer();
    }
  });

  prefersReducedMotion.addEventListener?.('change', resetTimer);

  showSlide(currentSlide);
  resetTimer();
});
