/* ========================
   PERSIAN DATE + CLOCK
======================== */
function toPersianNum(n) {
  const p = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
  return String(n).replace(/\d/g, d => p[d]);
}

function getPersianDate() {
  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth() + 1;
  const day = now.getDate();
  // Approximate Jalali conversion
  let jYear, jMonth, jDay;
  const g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
  const j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
  let gy = year - 1600,
    gm = month - 1,
    gd = day - 1;
  let g_day_no = 365 * gy + Math.floor((gy + 3) / 4) - Math.floor((gy + 99) / 100) + Math.floor((gy + 399) / 400);
  for (let i = 0; i < gm; i++) g_day_no += g_days_in_month[i];
  if (gm > 1 && ((gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0))) g_day_no++;
  g_day_no += gd;
  let j_day_no = g_day_no - 79;
  let j_np = Math.floor(j_day_no / 12053);
  j_day_no %= 12053;
  jYear = 979 + 33 * j_np + 4 * Math.floor(j_day_no / 1461);
  j_day_no %= 1461;
  if (j_day_no >= 366) {
    jYear += Math.floor((j_day_no - 1) / 365);
    j_day_no = (j_day_no - 1) % 365;
  }
  for (let i = 0; i < 11 && j_day_no >= j_days_in_month[i]; i++) {
    j_day_no -= j_days_in_month[i];
    jYear += (i === 11 ? 0 : 0);
    if (i < 11) jYear += 0;
  }
  let sum = 0;
  jMonth = 0;
  for (let i = 0; i < 12; i++) {
    if (j_day_no < sum + j_days_in_month[i]) {
      jMonth = i + 1;
      break;
    }
    sum += j_days_in_month[i];
  }
  jDay = j_day_no - sum + 1;
  const months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
  const days = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه'];
  const dayName = days[now.getDay()];
  return {
    full: `${toPersianNum(jDay)} ${months[jMonth-1]} ${toPersianNum(jYear)}`,
    withDay: `${dayName} ${toPersianNum(jDay)} ${months[jMonth-1]} ${toPersianNum(jYear)}`,
    compact: `${toPersianNum(jYear)}/${toPersianNum(String(jMonth).padStart(2,'0'))}/${toPersianNum(String(jDay).padStart(2,'0'))}`
  };
}

function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2, '0');
  const m = String(now.getMinutes()).padStart(2, '0');
  const s = String(now.getSeconds()).padStart(2, '0');
  const timeStr = toPersianNum(`${h}:${m}:${s}`);
  const el = document.getElementById('nav-clock');
  if (el) el.textContent = timeStr;
  const pd = getPersianDate();
  const dateEl = document.getElementById('nav-date-str');
  if (dateEl) dateEl.textContent = pd.full;
  const topDate = document.getElementById('top-shamsi-date');
  if (topDate) topDate.textContent = pd.withDay;
  const ph = document.getElementById('prayer-date-header');
  if (ph) ph.textContent = pd.withDay + ' — بجنورد';
}
updateClock();
setInterval(updateClock, 1000);





/* ========================
   SCROLL TO TOP
======================== */
window.addEventListener('scroll', () => {
  const btn = document.getElementById('scrollTop');
  btn.classList.toggle('show', window.scrollY > 300);
});

/* ========================
   FADE IN OBSERVER
======================== */
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, {
  threshold: 0.1
});
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));




/* ========================
   LOGO UPLOAD
======================== */
// function loadLogo(input, imgId, hintId) {
//   const file = input.files[0];
//   if (!file) return;
//   if (file.size > 2 * 1024 * 1024) {
//     alert('حجم فایل نباید بیشتر از ۲ مگابایت باشد');
//     return;
//   }
//   const reader = new FileReader();
//   reader.onload = e => {
//     const img = document.getElementById(imgId);
//     const hint = document.getElementById(hintId);
//     img.src = e.target.result;
//     img.style.display = 'block';
//     hint.style.display = 'none';
//   };
//   reader.readAsDataURL(file);
// }


/* ========================
   TICKER CLONE (seamless)
======================== */
const ticker = document.getElementById('ticker');
if (ticker) {
  const clone = ticker.innerHTML;
  ticker.innerHTML += clone;
}

// themes dark / light
function toggleTheme() {
  document.documentElement.classList.toggle("dark");

  const isDark = document.documentElement.classList.contains("dark");
  localStorage.setItem("theme", isDark ? "dark" : "light");

  // آپدیت آیکون
  const icon = document.querySelector(".theme-btn i");
  icon.className = isDark ? "bi bi-sun" : "bi bi-moon";
}

// هنگام لود صفحه هم آیکون رو درست کن
const themeIcon = document.querySelector(".theme-btn i");

if (localStorage.getItem("theme") === "dark") {
  document.documentElement.classList.add("dark");

  if (themeIcon) {
    themeIcon.className = "bi bi-sun";
  }
}

// document.addEventListener("DOMContentLoaded", () => {
//   if (localStorage.getItem("theme") === "dark") {
//     document.documentElement.classList.add("dark");
//     document.querySelector(".theme-btn i").className = "bi bi-sun";
//   }
// });