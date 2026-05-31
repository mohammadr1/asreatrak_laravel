
// ========================
// FADE IN OBSERVER
// ========================
// const observer = new IntersectionObserver((entries) => {
//   entries.forEach(e => {
//     if (e.isIntersecting) {
//       e.target.classList.add('visible');
//       observer.unobserve(e.target);
//     }
//   });
// }, { threshold: 0.08 });

// document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));




// ========================
// THEME TOGGLE
// ========================
// function toggleTheme() {
//   document.documentElement.classList.toggle("dark");

//   const isDark = document.documentElement.classList.contains("dark");
//   localStorage.setItem("theme", isDark ? "dark" : "light");

//   const icon = document.querySelector(".theme-btn i");
//   if (icon) icon.className = isDark ? "bi bi-sun" : "bi bi-moon";
// }


// // init theme
// (function () {
//   if (localStorage.getItem("theme") === "dark") {
//     document.documentElement.classList.add("dark");
//     const icon = document.querySelector(".theme-btn i");
//     if (icon) icon.className = "bi bi-sun";
//   }
// })();


// PICKER (JDP) — نسخه اصلاح‌شده
// ========================

// // --- تبدیل گرگوری به جلالی (اصلاح‌شده) ---
// function toJalali(gy, gm, gd) {
//   // !! مشکل قبلی: فرمول نیاز به سال از ۱۶۰۰ داشت، نه سال کامل
//   const g_days = [31,28,31,30,31,30,31,31,30,31,30,31];
//   const j_days = [31,31,31,31,31,31,30,30,30,30,30,29];
//   let gy2 = gy - 1600, gm2 = gm - 1, gd2 = gd - 1;
//   let g_day_no = 365*gy2 + Math.floor((gy2+3)/4) - Math.floor((gy2+99)/100) + Math.floor((gy2+399)/400);
//   for (let i = 0; i < gm2; i++) g_day_no += g_days[i];
//   if (gm2 > 1 && ((gy%4===0 && gy%100!==0) || gy%400===0)) g_day_no++;
//   g_day_no += gd2;
//   let j_day_no = g_day_no - 79;
//   let j_np = Math.floor(j_day_no / 12053); j_day_no %= 12053;
//   let jy = 979 + 33*j_np + 4*Math.floor(j_day_no/1461);
//   j_day_no %= 1461;
//   if (j_day_no >= 366) { jy += Math.floor((j_day_no-1)/365); j_day_no = (j_day_no-1)%365; }
//   let jm = 0;
//   for (; jm < 11 && j_day_no >= j_days[jm]; jm++) j_day_no -= j_days[jm];
//   return [jy, jm+1, j_day_no+1];
// }


// ========================
// JALALI HELPERS
// ========================
function toJalali(gy, gm, gd) {
  const g_days = [31,28,31,30,31,30,31,31,30,31,30,31];
  const j_days = [31,31,31,31,31,31,30,30,30,30,30,29];

  let gy2 = gy - 1600, gm2 = gm - 1, gd2 = gd - 1;

  let g_day_no =
    365 * gy2 +
    Math.floor((gy2 + 3) / 4) -
    Math.floor((gy2 + 99) / 100) +
    Math.floor((gy2 + 399) / 400);

  for (let i = 0; i < gm2; i++) g_day_no += g_days[i];
  if (gm2 > 1 && ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0)) g_day_no++;

  g_day_no += gd2;

  let j_day_no = g_day_no - 79;
  let j_np = Math.floor(j_day_no / 12053);
  j_day_no %= 12053;

  let jy = 979 + 33 * j_np + 4 * Math.floor(j_day_no / 1461);
  j_day_no %= 1461;

  if (j_day_no >= 366) {
    jy += Math.floor((j_day_no - 1) / 365);
    j_day_no = (j_day_no - 1) % 365;
  }

  let jm = 0;
  for (; jm < 11 && j_day_no >= j_days[jm]; jm++) {
    j_day_no -= j_days[jm];
  }

  return [jy, jm + 1, j_day_no + 1];
}


// --- تبدیل جلالی به گرگوری (اصلاح‌شده) ---
function toGregorian(jy, jm, jd) {
  const j_days = [31,31,31,31,31,31,30,30,30,30,30,29];
  jy -= 979; jm -= 1; jd -= 1;
  let j_day_no = 365*jy + Math.floor(jy/33)*8 + Math.floor((jy%33+3)/4);
  for (let i = 0; i < jm; i++) j_day_no += j_days[i];
  j_day_no += jd;
  let g_day_no = j_day_no + 79;
  let gy = 1600 + 400*Math.floor(g_day_no/146097); g_day_no %= 146097;
  let leap = true;
  if (g_day_no >= 36525) {
    g_day_no--;
    gy += 100*Math.floor(g_day_no/36524); g_day_no %= 36524;
    if (g_day_no >= 365) g_day_no++;
    else leap = false;
  }
  gy += 4*Math.floor(g_day_no/1461); g_day_no %= 1461;
  if (g_day_no >= 366) { leap = false; g_day_no--; gy += Math.floor(g_day_no/365); g_day_no %= 365; }
  const g_days = [31, leap?29:28, 31,30,31,30,31,31,30,31,30,31];
  let gm = 0;
  for (; gm < 12 && g_day_no >= g_days[gm]; gm++) g_day_no -= g_days[gm];
  return [gy, gm+1, g_day_no+1];
}

// --- تعداد روزهای هر ماه جلالی ---
function jalaliMonthDays(jy, jm) {
  if (jm <= 6) return 31;
  if (jm <= 11) return 30;
  return ((jy - (jy > 0 ? 474 : 473)) % 2820 + 474 + 38) * 682 % 2816 < 682 ? 30 : 29;
}

function toPNum(n) {
  return String(n).replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[+d]);
}

const J_MONTHS = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
const J_DOW    = ['ش','ی','د','س','چ','پ','ج'];

function jalaliFirstDayOfWeek(jy, jm) {
  const [gy, gm, gd] = toGregorian(jy, jm, 1);
  return (new Date(gy, gm-1, gd).getDay() + 1) % 7; // شنبه=0
}

// ========================
// کلاس اصلی تقویم
// ========================
class JalaliPicker {
  constructor({ inputId, pickerId, onChange }) {
    this.input  = document.getElementById(inputId);
    this.picker = document.getElementById(pickerId);
    this.onChange = onChange || null;

    const now = new Date();
    this.today    = toJalali(now.getFullYear(), now.getMonth()+1, now.getDate());
    this.curYear  = this.today[0];
    this.curMonth = this.today[1];
    this.selected = null;
    this.view     = 'days';

    this._bindToggle();
    this._bindOutside();
    this.render();
  }

  open()   { this.picker.classList.add('open'); }
  close()  { this.picker.classList.remove('open'); }
  toggle() { this.picker.classList.contains('open') ? this.close() : this.open(); }

  _bindToggle() {
    this.input.addEventListener('click', () => this.toggle());
    const wrap = this.input.closest('.jalali-input-wrap');
    if (wrap) {
      const icon = wrap.querySelector('.bi');
      if (icon) icon.style.pointerEvents = 'auto';
      if (icon) icon.addEventListener('click', (e) => { e.stopPropagation(); this.toggle(); });
    }
  }

  _bindOutside() {
    document.addEventListener('click', (e) => {
      const wrap = this.picker.closest('.filter-group') || this.picker.parentElement;
      if (!wrap.contains(e.target)) this.close();
    });
    this.picker.addEventListener('click', e => e.stopPropagation());
  }

  render() {
    if (this.view === 'months') { this._renderMonths(); return; }
    if (this.view === 'years')  { this._renderYears();  return; }
    this._renderDays();
  }

  _renderDays() {
    const { curYear: jy, curMonth: jm } = this;
    const daysInMonth = jalaliMonthDays(jy, jm);
    const firstDow    = jalaliFirstDayOfWeek(jy, jm);

    // آیا ماه جاری در آینده است؟ (برای غیرفعال‌سازی روزهای آینده در ماه جاری)
    const todayY = this.today[0], todayM = this.today[1], todayD = this.today[2];
    const isFutureMonth = (jy > todayY) || (jy === todayY && jm > todayM);

    let html = `
      <div class="jdp-header">
        <button class="jdp-nav" data-action="prev"><i class="bi bi-chevron-right"></i></button>
        <span class="jdp-title" data-action="months">${J_MONTHS[jm-1]} ${toPNum(jy)}</span>
        <button class="jdp-nav" data-action="next" ${isFutureMonth?'disabled style="opacity:.3;cursor:default"':''}><i class="bi bi-chevron-left"></i></button>
      </div>
      <div class="jdp-dow">${J_DOW.map(d=>`<span>${d}</span>`).join('')}</div>
      <div class="jdp-days">`;

    for (let i = 0; i < firstDow; i++) html += `<div class="jdp-day empty"></div>`;

    for (let d = 1; d <= daysInMonth; d++) {
      const isToday   = (jy===todayY && jm===todayM && d===todayD);
      const isSel     = this.selected && (jy===this.selected[0] && jm===this.selected[1] && d===this.selected[2]);
      // روزهای آینده در ماه جاری غیرفعال
      const isFuture  = (jy === todayY && jm === todayM && d > todayD) || (jy === todayY && jm > todayM) || jy > todayY;
      let cls = 'jdp-day' + (isToday?' today':'') + (isSel?' selected':'') + (isFuture?' other':'');
      const attr = isFuture ? ' style="opacity:.35;cursor:default" data-disabled="1"' : `data-day="${d}"`;
      html += `<div class="${cls}"${attr}>${toPNum(d)}</div>`;
    }

    html += `</div>
      <div class="jdp-footer">
        <button class="jdp-btn jdp-btn-clear" data-action="clear">پاک</button>
        <button class="jdp-btn jdp-btn-ok" data-action="ok">تأیید</button>
      </div>`;

    this.picker.innerHTML = html;
    this._bindDayEvents();
  }

  _renderMonths() {
    const todayY = this.today[0], todayM = this.today[1];
    let html = `
      <div class="jdp-header">
        <button class="jdp-nav" data-action="prevYear"><i class="bi bi-chevron-right"></i></button>
        <span class="jdp-title" data-action="years">${toPNum(this.curYear)}</span>
        <button class="jdp-nav" data-action="nextYear" ${this.curYear>=todayY?'disabled style="opacity:.3;cursor:default"':''}><i class="bi bi-chevron-left"></i></button>
      </div>
      <div class="jdp-months">`;

    J_MONTHS.forEach((m, i) => {
      const isSel    = (this.curMonth === i+1);
      // ماه‌های آینده در سال جاری غیرفعال
      const isFuture = (this.curYear === todayY && (i+1) > todayM) || this.curYear > todayY;
      const cls = `jdp-month-item${isSel?' sel':''}${isFuture?' other':''}`;
      const attr = isFuture
        ? ' style="opacity:.35;cursor:default"'
        : ` data-month="${i+1}"`;
      html += `<div class="${cls}"${attr}>${m}</div>`;
    });

    html += `</div>
      <div class="jdp-footer">
        <button class="jdp-btn jdp-btn-clear" data-action="backDays">بازگشت</button>
      </div>`;
    this.picker.innerHTML = html;
    this._bindMonthEvents();
  }

  _renderYears() {
    const todayY = this.today[0];
    // دهه فعلی بر اساس سال جاری تقویم
    const base = this.curYear - (this.curYear % 10);
    // نمایش فقط سال‌هایی که هنوز نگذشته یا امسال هستند
    const maxY = todayY;

    let html = `
      <div class="jdp-header">
        <button class="jdp-nav" data-action="prevDecade"><i class="bi bi-chevron-right"></i></button>
        <span class="jdp-title">${toPNum(base)}–${toPNum(Math.min(base+9, maxY))}</span>
        <button class="jdp-nav" data-action="nextDecade" ${base+10 > maxY?'disabled style="opacity:.3;cursor:default"':''}><i class="bi bi-chevron-left"></i></button>
      </div>
      <div class="jdp-year-grid jdp-years">`;

    for (let y = base; y <= base + 9; y++) {
      if (y > maxY) break; // سال‌های آینده نمایش داده نمی‌شوند
      const isSel = (y === this.curYear);
      html += `<div class="jdp-year-item${isSel?' sel':''}" data-year="${y}">${toPNum(y)}</div>`;
    }

    html += `</div>
      <div class="jdp-footer">
        <button class="jdp-btn jdp-btn-clear" data-action="backMonths">بازگشت</button>
      </div>`;
    this.picker.innerHTML = html;
    this._bindYearEvents(base, maxY);
  }

  _bindDayEvents() {
    this.picker.querySelectorAll('[data-day]').forEach(el => {
      el.addEventListener('click', () => {
        this.selected = [this.curYear, this.curMonth, +el.dataset.day];
        this.render();
      });
    });
    this.picker.querySelector('[data-action="prev"]')?.addEventListener('click', () => {
      if (--this.curMonth < 1) { this.curMonth = 12; this.curYear--; }
      this.render();
    });
    const nextBtn = this.picker.querySelector('[data-action="next"]');
    if (nextBtn && !nextBtn.disabled) {
      nextBtn.addEventListener('click', () => {
        if (++this.curMonth > 12) { this.curMonth = 1; this.curYear++; }
        this.render();
      });
    }
    this.picker.querySelector('[data-action="months"]')?.addEventListener('click', () => {
      this.view = 'months'; this.render();
    });
    this.picker.querySelector('[data-action="ok"]')?.addEventListener('click', () => {
      if (this.selected) {
        const [y,m,d] = this.selected;
        this.input.value = `${toPNum(y)}/${toPNum(String(m).padStart(2,'0'))}/${toPNum(String(d).padStart(2,'0'))}`;
        if (this.onChange) this.onChange(y, m, d);
      }
      this.close();
    });
    this.picker.querySelector('[data-action="clear"]')?.addEventListener('click', () => {
      this.selected = null; this.input.value = ''; this.render(); this.close();
    });
  }

  _bindMonthEvents() {
    this.picker.querySelectorAll('[data-month]').forEach(el => {
      el.addEventListener('click', () => {
        this.curMonth = +el.dataset.month; this.view = 'days'; this.render();
      });
    });
    this.picker.querySelector('[data-action="prevYear"]')?.addEventListener('click', () => { this.curYear--; this.render(); });
    const nextYBtn = this.picker.querySelector('[data-action="nextYear"]');
    if (nextYBtn && !nextYBtn.disabled) {
      nextYBtn.addEventListener('click', () => { this.curYear++; this.render(); });
    }
    this.picker.querySelector('[data-action="years"]')?.addEventListener('click', () => { this.view = 'years'; this.render(); });
    this.picker.querySelector('[data-action="backDays"]')?.addEventListener('click', () => { this.view = 'days'; this.render(); });
  }

  _bindYearEvents(base, maxY) {
    this.picker.querySelectorAll('[data-year]').forEach(el => {
      el.addEventListener('click', () => {
        this.curYear = +el.dataset.year; this.view = 'months'; this.render();
      });
    });
    this.picker.querySelector('[data-action="prevDecade"]')?.addEventListener('click', () => {
      this.curYear = base - 10; this.render();
    });
    const nextDBtn = this.picker.querySelector('[data-action="nextDecade"]');
    if (nextDBtn && !nextDBtn.disabled) {
      nextDBtn.addEventListener('click', () => { this.curYear = base + 10; this.render(); });
    }
    this.picker.querySelector('[data-action="backMonths"]')?.addEventListener('click', () => { this.view = 'months'; this.render(); });
  }
}




// ========================
// راه‌اندازی (بدون DOMContentLoaded تکراری!)
// ========================
const fromDate = document.getElementById('fromDate');
const fromPicker = document.getElementById('fromPicker');

new JalaliPicker({
  inputId: "fromDate",
  pickerId: "fromPicker",
      onChange: (y, m, d) => { /* می‌توانی فیلتر را اینجا اعمال کنی */ }
});

const toDate = document.getElementById('toDate');
const toPicker = document.getElementById('toPicker');

new JalaliPicker({
  inputId: "toDate",
  pickerId: "toPicker",
    onChange: (y, m, d) => { /* می‌توانی فیلتر را اینجا اعمال کنی */ }
});


document.addEventListener("DOMContentLoaded", function () {

  let selectedFromDay = '';
  let selectedToDay = '';

  const fromPicker = document.getElementById('fromPicker');
  const toPicker = document.getElementById('toPicker');

  const fromInput = document.getElementById('fromDate');
  const toInput = document.getElementById('toDate');

  const fromWrap = document.getElementById('fromWrap');
  const toWrap = document.getElementById('toWrap');

  // =========================
  // باز کردن
  // =========================
  if (fromWrap) {
    fromWrap.addEventListener('click', function (e) {
      e.stopPropagation();
      fromPicker.classList.add('open');
      toPicker.classList.remove('open');
    });
  }

  if (toWrap) {
    toWrap.addEventListener('click', function (e) {
      e.stopPropagation();
      toPicker.classList.add('open');
      fromPicker.classList.remove('open');
    });
  }
  // =========================
  // جلوگیری از بسته شدن با کلیک داخل
  // =========================
  if(fromPicker){
    fromPicker.addEventListener('click', e => e.stopPropagation());
  }
  if (toPicker) {
    toPicker.addEventListener('click', e => e.stopPropagation());
  }
  // =========================
  // انتخاب روز (فقط ذخیره، نه بستن)
  // =========================
  document.querySelectorAll('#fromPicker .jdp-day').forEach(day => {
    day.addEventListener('click', function () {
      selectedFromDay = this.dataset.day;
    });
  });

  document.querySelectorAll('#toPicker .jdp-day').forEach(day => {
    day.addEventListener('click', function () {
      selectedToDay = this.dataset.day;
    });
  });

  // =========================
  // دکمه تایید
  // =========================
  if (fromWrap && fromPicker) {
    document.querySelector('#fromPicker .jdp-btn-ok').addEventListener('click', function () {
      const month = document.querySelector('#fromPicker .jdp-title').innerText;
      if (selectedFromDay) {
        fromInput.value = selectedFromDay + ' ' + month;
      }
      fromPicker.classList.remove('open');
    });
  }

  document.querySelector('#toPicker .jdp-btn-ok').addEventListener('click', function () {
    const month = document.querySelector('#toPicker .jdp-title').innerText;
    if (selectedToDay) {
      toInput.value = selectedToDay + ' ' + month;
    }
    toPicker.classList.remove('open');
  });

  // =========================
  // دکمه پاک کردن
  // =========================
  document.querySelector('#fromPicker .jdp-btn-clear').addEventListener('click', function () {
    selectedFromDay = '';
    fromInput.value = '';
  });

  document.querySelector('#toPicker .jdp-btn-clear').addEventListener('click', function () {
    selectedToDay = '';
    toInput.value = '';
  });

  // =========================
  // بستن با کلیک بیرون
  // =========================
  document.addEventListener('click', function () {
    fromPicker.classList.remove('open');
    toPicker.classList.remove('open');
  });

});



  // =========================
  // متد تغییر وضعیت خبر دو ستونه به تک ستونه
  // =========================

function setView(view){

    const archiveGrid = document.getElementById('archiveGrid');
    const archiveList = document.getElementById('archiveList');

    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');

    if(view === 'grid'){

        archiveGrid.classList.remove('hide');
        archiveList.classList.remove('show');

        gridBtn.classList.add('active');
        listBtn.classList.remove('active');

        localStorage.setItem('archiveView', 'grid');

    }else{

        archiveGrid.classList.add('hide');
        archiveList.classList.add('show');

        listBtn.classList.add('active');
        gridBtn.classList.remove('active');

        localStorage.setItem('archiveView', 'list');
    }
}

document.addEventListener('DOMContentLoaded', () => {

    const savedView = localStorage.getItem('archiveView') || 'grid';

    setView(savedView);

});




