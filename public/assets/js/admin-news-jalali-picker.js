(function () {
    'use strict';

    if (window.__adminJalaliPickerLoaded) return;
    window.__adminJalaliPickerLoaded = true;

    const months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
    const weekdays = ['ش','ی','د','س','چ','پ','ج'];
    const fa = value => String(value).replace(/\d/g, digit => '۰۱۲۳۴۵۶۷۸۹'[digit]);
    const en = value => String(value).replace(/[۰-۹]/g, digit => '۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)).replace(/[٠-٩]/g, digit => '٠١٢٣٤٥٦٧٨٩'.indexOf(digit));
    const pad = value => String(value).padStart(2, '0');

    function toJalali(gy, gm, gd) {
        const gDays = [31,28,31,30,31,30,31,31,30,31,30,31], jDays = [31,31,31,31,31,31,30,30,30,30,30,29];
        let gy2 = gy - 1600, gm2 = gm - 1, gd2 = gd - 1;
        let day = 365 * gy2 + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400);
        for (let i = 0; i < gm2; i++) day += gDays[i];
        if (gm2 > 1 && ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0)) day++;
        day += gd2;
        let jDay = day - 79, cycle = Math.floor(jDay / 12053); jDay %= 12053;
        let jy = 979 + 33 * cycle + 4 * Math.floor(jDay / 1461); jDay %= 1461;
        if (jDay >= 366) { jy += Math.floor((jDay - 1) / 365); jDay = (jDay - 1) % 365; }
        let jm = 0; for (; jm < 11 && jDay >= jDays[jm]; jm++) jDay -= jDays[jm];
        return [jy, jm + 1, jDay + 1];
    }

    function toGregorian(jy, jm, jd) {
        const jDays = [31,31,31,31,31,31,30,30,30,30,30,29];
        jy -= 979; jm -= 1; jd -= 1;
        let day = 365 * jy + Math.floor(jy / 33) * 8 + Math.floor((jy % 33 + 3) / 4);
        for (let i = 0; i < jm; i++) day += jDays[i]; day += jd;
        let gDay = day + 79, gy = 1600 + 400 * Math.floor(gDay / 146097), leap = true;
        gDay %= 146097;
        if (gDay >= 36525) { gDay--; gy += 100 * Math.floor(gDay / 36524); gDay %= 36524; if (gDay >= 365) gDay++; else leap = false; }
        gy += 4 * Math.floor(gDay / 1461); gDay %= 1461;
        if (gDay >= 366) { leap = false; gDay--; gy += Math.floor(gDay / 365); gDay %= 365; }
        const gDays = [31, leap ? 29 : 28,31,30,31,30,31,31,30,31,30,31];
        let gm = 0; for (; gm < 12 && gDay >= gDays[gm]; gm++) gDay -= gDays[gm];
        return [gy, gm + 1, gDay + 1];
    }

    const monthDays = (year, month) => {
        const start = toGregorian(year, month, 1);
        const end = toGregorian(month === 12 ? year + 1 : year, month === 12 ? 1 : month + 1, 1);
        return (Date.UTC(end[0], end[1] - 1, end[2]) - Date.UTC(start[0], start[1] - 1, start[2])) / 86400000;
    };
    const firstDay = (year, month) => { const [gy, gm, gd] = toGregorian(year, month, 1); return (new Date(gy, gm - 1, gd).getDay() + 1) % 7; };

    const selector = '[data-jalali-date-input="true"]';
    const parse = value => {
        const match = en(value || '').match(/^(1[34]\d{2})\/(\d{2})\/(\d{2})(?: (\d{2}):(\d{2}))?$/);
        if (!match) return null;
        const date = [+match[1], +match[2], +match[3]];
        if (date[1] < 1 || date[1] > 12 || date[2] < 1 || date[2] > monthDays(...date.slice(0, 2))) return null;
        if (match[4] !== undefined && (+match[4] > 23 || +match[5] > 59)) return null;
        return { date, time: match[4] !== undefined, hour: +(match[4] || 0), minute: +(match[5] || 0) };
    };
    // Numeric ordering is sufficient for Jalali dates; no timezone conversion is needed.
    const stamp = (date, hour = 0, minute = 0) => ((date[0] * 13 + date[1]) * 32 + date[2]) * 1440 + hour * 60 + minute;
    const format = date => `${date[0]}/${pad(date[1])}/${pad(date[2])}`;
    let pickerId = 0;

    class JalaliDatePicker {
        constructor(input) {
            this.input = input;
            this.popup = document.createElement('div');
            this.popup.className = 'admin-jalali-picker';
            this.popup.id = `admin-jalali-picker-${++pickerId}`;
            this.popup.setAttribute('role', 'dialog');
            this.popup.setAttribute('aria-label', 'انتخاب تاریخ و ساعت شمسی');
            this.input.setAttribute('aria-haspopup', 'dialog');
            this.input.setAttribute('aria-controls', this.popup.id);
            this.input.setAttribute('aria-expanded', 'false');
            this.timeEnabled = input.dataset.jalaliTime === 'true';
            this.timeRequired = input.dataset.jalaliTimeRequired === 'true';
            const now = new Date();
            this.today = toJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());
            this.popup._input = input; this.popup._picker = this;
            document.body.appendChild(this.popup);
            input.addEventListener('click', () => this.toggle());
            input.addEventListener('keydown', event => {
                if (['Enter', ' ', 'ArrowDown'].includes(event.key)) { event.preventDefault(); this.open(); this.focusFirst(); }
                if (event.key === 'Escape') this.close();
            });
            input.addEventListener('input', () => this.syncRange());
            this.popup.addEventListener('click', event => this.click(event));
            this.popup.addEventListener('change', event => this.changeTime(event));
            this.popup.addEventListener('keydown', event => {
                if (event.key === 'Escape') { event.preventDefault(); this.close(true); }
            });
        }
        read() { return parse(this.input.value)?.date || null; }
        startInput() {
            if (this.input.dataset.jalaliRole !== 'until') return null;
            return Array.from(document.querySelectorAll(selector)).find(input =>
                input.dataset.jalaliRange === this.input.dataset.jalaliRange && input.dataset.jalaliRole === 'from'
                && input.closest('[wire\\:id]') === this.input.closest('[wire\\:id]'));
        }
        minimum() { return parse(this.startInput()?.value); }
        allowed(date, hour = 23, minute = 59) {
            if (date[0] < 1300 || date[0] > 1499) return false;
            const min = this.minimum();
            return !min || stamp(date, hour, minute) >= stamp(min.date, min.hour, min.minute);
        }
        syncRange() {
            if (this.input.dataset.jalaliRole !== 'from') return;
            document.querySelectorAll(selector).forEach(input => {
                const picker = input._jalaliPicker;
                if (!picker || picker.startInput() !== this.input) return;
                const end = parse(input.value);
                if (end && !picker.allowed(end.date, end.time ? end.hour : 23, end.time ? end.minute : 59)) {
                    picker.write('');
                    picker.notice = 'پایان قبلی از شروع زودتر بود؛ لطفاً دوباره انتخاب کنید.';
                }
                if (picker.popup.classList.contains('is-open')) picker.open();
            });
        }
        toggle() { this.popup.classList.contains('is-open') ? this.close() : this.open(); }
        open() {
            document.querySelectorAll('.admin-jalali-picker.is-open').forEach(popup => popup._picker?.close());
            const value = parse(this.input.value);
            this.selected = value?.date || null;
            this.includeTime = this.timeEnabled && (this.timeRequired || !!value?.time);
            this.hour = value?.time ? value.hour : (this.input.dataset.jalaliRole === 'until' ? 23 : 0);
            this.minute = value?.time ? value.minute : (this.input.dataset.jalaliRole === 'until' ? 59 : 0);
            this.view = (this.selected || this.minimum()?.date || this.today).slice();
            this.mode = 'days';
            this.yearPage = Math.floor(this.view[0] / 12) * 12;
            this.render();
            this.popup.classList.add('is-open');
            this.input.setAttribute('aria-expanded', 'true');
            this.position();
        }
        close(focus = false) {
            this.popup.classList.remove('is-open');
            this.input.setAttribute('aria-expanded', 'false');
            if (focus) this.input.focus({ preventScroll: true });
        }
        focusFirst() { this.popup.querySelector?.('button:not(:disabled)')?.focus({ preventScroll: true }); }
        position() {
            const rect = this.input.getBoundingClientRect();
            const width = this.popup.offsetWidth || Math.min(344, innerWidth - 16);
            const height = this.popup.offsetHeight || 440;
            const top = rect.bottom + 8 + height <= innerHeight ? rect.bottom + 8 : Math.max(8, rect.top - height - 8);
            this.popup.style.top = `${top}px`;
            this.popup.style.left = `${Math.max(8, Math.min(rect.left, innerWidth - width - 8))}px`;
        }
        navigation(delta) {
            if (this.mode === 'years') return this.yearPage + delta * 12;
            if (this.mode === 'months') return this.view[0] + delta;
            const date = new Date(this.view[0], this.view[1] - 1 + delta, 1);
            return [date.getFullYear(), date.getMonth() + 1];
        }
        canNavigate(delta) {
            const value = this.navigation(delta);
            if (this.mode === 'years') return value <= 1499 && value + 11 >= 1300;
            const year = this.mode === 'months' ? value : value[0];
            return year >= 1300 && year <= 1499;
        }
        render() {
            const [year, month] = this.view;
            const disabled = condition => condition ? ' disabled aria-disabled="true"' : '';
            const active = condition => condition ? 'is-selected' : '';
            let html = `<div class="admin-jalali-picker__caption"><span>تقویم شمسی</span><button type="button" data-action="close" aria-label="بستن تقویم">×</button></div>
                <div class="admin-jalali-picker__head"><button type="button" data-action="prev" aria-label="بازه قبلی"${disabled(!this.canNavigate(-1))}>‹</button>
                <div class="admin-jalali-picker__title"><button type="button" data-action="month" aria-label="انتخاب ماه">${months[month - 1]}</button><button type="button" data-action="year" aria-label="انتخاب سال">${fa(year)}</button></div>
                <button type="button" data-action="next" aria-label="بازه بعدی"${disabled(!this.canNavigate(1))}>›</button></div>`;
            if (this.mode === 'years') {
                html += `<p class="admin-jalali-picker__hint">انتخاب سال؛ ${fa(Math.max(1300, this.yearPage))} تا ${fa(Math.min(1499, this.yearPage + 11))}</p><div class="admin-jalali-picker__options">`;
                for (let y = Math.max(1300, this.yearPage); y <= Math.min(1499, this.yearPage + 11); y++) html += `<button type="button" data-year="${y}" class="${active(y === year)}">${fa(y)}</button>`;
            } else if (this.mode === 'months') {
                html += '<p class="admin-jalali-picker__hint">انتخاب ماه</p><div class="admin-jalali-picker__options">';
                months.forEach((name, index) => { html += `<button type="button" data-month="${index + 1}" class="${active(index + 1 === month)}">${name}</button>`; });
            } else {
                html += `<div class="admin-jalali-picker__week">${weekdays.map(day => `<span>${day}</span>`).join('')}</div><div class="admin-jalali-picker__days">`;
                for (let i = 0; i < firstDay(year, month); i++) html += '<span></span>';
                for (let day = 1; day <= monthDays(year, month); day++) {
                    const date = [year, month, day], selected = this.selected && format(date) === format(this.selected);
                    html += `<button type="button" class="${active(selected)}" data-day="${day}" aria-label="${fa(day)} ${months[month - 1]} ${fa(year)}" aria-pressed="${!!selected}"${disabled(!this.allowed(date))}>${fa(day)}</button>`;
                }
            }
            html += '</div>';
            if (this.timeEnabled && this.mode === 'days') {
                html += `<div class="admin-jalali-picker__time"><label><input type="checkbox" data-time="enabled" ${this.includeTime ? 'checked' : ''} ${this.timeRequired ? 'disabled' : ''}> ${this.timeRequired ? 'ساعت انتشار (به وقت تهران)' : 'ساعت دقیق (اختیاری)'}</label>`;
                if (this.includeTime) {
                    const date = this.selected || this.view;
                    const options = (count, current, field) => Array.from({ length: count }, (_, n) => `<option value="${n}" ${n === current ? 'selected' : ''}${disabled(!this.allowed(date, field === 'hour' ? n : this.hour, field === 'hour' ? 59 : n))}>${fa(pad(n))}</option>`).join('');
                    html += `<div class="admin-jalali-picker__clock" dir="ltr"><label>ساعت<select data-time="hour" aria-label="ساعت">${options(24, this.hour, 'hour')}</select></label><span>:</span><label>دقیقه<select data-time="minute" aria-label="دقیقه">${options(60, this.minute, 'minute')}</select></label></div>`;
                }
                html += `<p class="admin-jalali-picker__hint">${this.timeRequired ? 'تاریخ و ساعت انتخاب‌شده پس از تأیید در فرم قرار می‌گیرد.' : 'بدون ساعت: تمام روز · با ساعت: تا پایان دقیقه'}</p></div>`;
            }
            const min = this.minimum();
            if (min) html += `<p class="admin-jalali-picker__hint">حداقل پایان: <b dir="ltr">${fa(format(min.date))}${min.time ? ' ' + fa(pad(min.hour) + ':' + pad(min.minute)) : ''}</b><br>مرور ماه‌ها و سال‌ها آزاد است؛ روزهای قبل از شروع قابل انتخاب نیستند.</p>`;
            if (this.timeEnabled && this.selected) html += `<p class="admin-jalali-picker__hint" role="status">انتخاب شما: <b dir="ltr">${fa(format(this.selected))}${this.includeTime ? ' ' + fa(pad(this.hour) + ':' + pad(this.minute)) : ''}</b></p>`;
            if (this.notice) html += `<p class="admin-jalali-picker__notice" role="status">${this.notice}</p>`;
            html += `<div class="admin-jalali-picker__foot"><button type="button" data-action="today"${disabled(!this.allowed(this.today))}>امروز</button><button type="button" data-action="clear">پاک کردن</button>`;
            if (this.timeEnabled) html += `<button type="button" class="admin-jalali-picker__confirm" data-action="confirm"${disabled(!this.selected || !this.allowed(this.selected, this.includeTime ? this.hour : 23, this.includeTime ? this.minute : 59))}>تأیید انتخاب</button>`;
            html += '</div>';
            this.popup.innerHTML = html;
        }
        clampTime() {
            const min = this.minimum();
            if (min && this.selected && this.includeTime && !this.allowed(this.selected, this.hour, this.minute)) {
                this.hour = min.hour; this.minute = min.minute;
            }
        }
        changeTime(event) {
            const field = event.target.dataset.time;
            if (!field) return;
            if (field === 'enabled') this.includeTime = this.timeRequired || event.target.checked;
            if (field === 'hour' || field === 'minute') this[field] = Number(event.target.value);
            this.clampTime(); this.render(); this.position();
            this.popup.querySelector?.(`[data-time="${field}"]`)?.focus({ preventScroll: true });
        }
        write(value) {
            this.input.value = value;
            this.input.dispatchEvent(new Event('input', { bubbles: true }));
            this.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        commit() {
            if (!this.selected || !this.allowed(this.selected, this.includeTime ? this.hour : 23, this.includeTime ? this.minute : 59)) return;
            this.write(format(this.selected) + (this.includeTime ? ` ${pad(this.hour)}:${pad(this.minute)}` : ''));
            this.notice = ''; this.close(true);
        }
        click(event) {
            const target = event.target.closest('[data-action],[data-day],[data-month],[data-year]');
            if (!target || target.disabled) return;
            const data = target.dataset;
            if (data.year && +data.year >= 1300 && +data.year <= 1499) { this.view[0] = +data.year; this.mode = 'months'; }
            if (data.month && +data.month >= 1 && +data.month <= 12) { this.view[1] = +data.month; this.mode = 'days'; }
            if (data.day) {
                const date = [this.view[0], this.view[1], +data.day];
                if (!this.allowed(date)) return;
                this.selected = date; this.clampTime();
                if (!this.timeEnabled) { this.commit(); return; }
            }
            const action = data.action;
            if (action === 'month') this.mode = 'months';
            if (action === 'year') { this.mode = 'years'; this.yearPage = Math.floor(this.view[0] / 12) * 12; }
            if (action === 'prev' || action === 'next') {
                const delta = action === 'next' ? 1 : -1;
                if (!this.canNavigate(delta)) return;
                const value = this.navigation(delta);
                if (this.mode === 'years') this.yearPage = value;
                else if (this.mode === 'months') this.view[0] = value;
                else this.view = [...value, 1];
            }
            if (action === 'today' && this.allowed(this.today)) {
                this.selected = this.today.slice(); this.view = this.today.slice(); this.mode = 'days'; this.clampTime();
                if (!this.timeEnabled) { this.commit(); return; }
            }
            if (action === 'clear') { this.selected = null; this.write(''); this.close(true); return; }
            if (action === 'confirm') { this.commit(); return; }
            if (action === 'close') { this.close(true); return; }
            this.render(); this.position();
            if (data.year || data.month || action === 'year' || action === 'month') this.focusFirst();
            else this.popup.querySelector?.(data.day ? `[data-day="${data.day}"]` : `[data-action="${action}"]`)?.focus({ preventScroll: true });
        }
    }

    function boot() {
        document.querySelectorAll('.admin-jalali-picker').forEach(popup => {
            if (popup._input?.isConnected === false) popup.remove();
        });
        document.querySelectorAll('[data-jalali-date-input="true"], input[name$="[date_range][from]"], input[name$="[date_range][until]"]').forEach(input => {
            if (!input._jalaliPicker) input._jalaliPicker = new JalaliDatePicker(input);
        });
    }
    // Filament can mount the filter form lazily. Capture the first click as a
    // fallback so the picker is initialized even if the form appeared after
    // the observer ran.
    document.addEventListener('click', event => {
        const input = event.target.closest?.('[data-jalali-date-input="true"], input[name*="date_range"][name*="from"], input[name*="date_range"][name*="until"]');
        if (input && !input._jalaliPicker) {
            input._jalaliPicker = new JalaliDatePicker(input);
            setTimeout(() => input._jalaliPicker.open(), 0);
        }
    }, true);
    document.addEventListener('click', event => {
        // Rendering replaces the clicked node. Its original event path still
        // identifies the popup, unlike contains(event.target) after rendering.
        const path = event.composedPath?.() || [];
        document.querySelectorAll('.admin-jalali-picker.is-open').forEach(popup => {
            if (!path.includes(popup) && !popup.contains(event.target) && popup._input !== event.target) popup._picker.close();
        });
    });
    document.addEventListener('focusin', event => { document.querySelectorAll('.admin-jalali-picker.is-open').forEach(popup => { if (!popup.contains(event.target) && popup._input !== event.target) popup._picker.close(); }); });
    addEventListener('resize', () => document.querySelectorAll('.admin-jalali-picker.is-open').forEach(popup => popup._picker?.position()));
    addEventListener('scroll', event => document.querySelectorAll('.admin-jalali-picker.is-open').forEach(popup => {
        if (!popup.contains(event.target)) popup._picker?.position();
    }), true);
    document.addEventListener('DOMContentLoaded', boot); addEventListener('livewire:navigated', boot); boot();
    if (document.body) new MutationObserver(boot).observe(document.body, { childList: true, subtree: true });
})();
