const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../public/assets/js/admin-news-jalali-picker.js'), 'utf8');

// Execute the production script against a small DOM/event test double.
// These are runtime regression tests, not browser layout tests.
function environment() {
    const nodes = [];
    const documentListeners = [];
    const windowListeners = [];
    const timers = [];
    class Element {
        constructor(input = false) {
            this.input = input;
            this.value = '';
            this.dataset = {};
            this.style = {};
            this.listeners = [];
            this.events = [];
            this.className = '';
            this.revision = 0;
            this.classList = {
                contains: name => this.className.split(' ').includes(name),
                add: name => { if (!this.classList.contains(name)) this.className += ' ' + name; },
                remove: name => { this.className = this.className.split(' ').filter(item => item !== name).join(' '); },
            };
        }
        addEventListener(type, fn) { this.listeners.push({ type, fn }); }
        setAttribute(name, value) { this[name] = value; }
        focus() {}
        getBoundingClientRect() { return { left: 200, top: 150, bottom: 194 }; }
        set innerHTML(value) { this.html = value; this.revision++; }
        get innerHTML() { return this.html; }
        contains(target) { return this === target || (target.parent === this && target.parentRevision === this.revision); }
        closest(selector) { return this.input && selector.includes('data-jalali') ? this : null; }
        dispatchEvent(event) {
            this.events.push(event.type);
            this.listeners.filter(item => item.type === event.type).forEach(item => item.fn(event));
        }
    }
    const document = {
        body: { appendChild: node => nodes.push(node) },
        createElement: () => new Element(),
        addEventListener: (type, fn, capture) => documentListeners.push({ type, fn, capture }),
        querySelectorAll: selector => nodes.filter(node => selector.includes('data-jalali') ? node.input : node.classList.contains('is-open')),
    };
    const context = vm.createContext({
        window: {}, document, Date, Event, innerWidth: 1280, innerHeight: 900,
        addEventListener: (type, fn) => windowListeners.push({ type, fn }),
        setTimeout: fn => timers.push(fn),
        MutationObserver: class { constructor(fn) { this.callback = fn; } observe() {} },
    });
    const addInput = (value, dataset = {}) => { const input = new Element(true); input.value = value || ''; input.dataset = dataset; nodes.push(input); return input; };
    function click(target) {
        const event = { target, type: 'click' };
        documentListeners.filter(item => item.type === 'click' && item.capture).forEach(item => item.fn(event));
        target.dispatchEvent(event);
        documentListeners.filter(item => item.type === 'click' && !item.capture).forEach(item => item.fn(event));
        timers.splice(0).forEach(fn => fn());
    }
    function action(picker, dataset) {
        const button = new Element();
        button.dataset = dataset;
        button.closest = () => button;
        button.parent = picker.popup;
        button.parentRevision = picker.popup.revision;
        // The event path is captured before render replaces its clicked button.
        const event = { type: 'click', target: button, composedPath: () => [button, picker.popup, document] };
        picker.popup.dispatchEvent(event);
        documentListeners.filter(item => item.type === 'click' && !item.capture).forEach(item => item.fn(event));
    }
    return { addInput, click, action, nodes, run: () => vm.runInContext(source, context), windowListeners };
}

test('click executes date conversion and opens the calendar with day buttons', () => {
    const env = environment(), input = env.addInput('1405/07/05');
    env.run();
    env.click(input);
    assert.equal(input._jalaliPicker.popup.classList.contains('is-open'), true);
    assert.match(input._jalaliPicker.popup.innerHTML, /مهر/);
    assert.equal((input._jalaliPicker.popup.innerHTML.match(/data-day=/g) || []).length, 30);
});

test('selecting a day updates the input, emits Livewire events, and closes', () => {
    const env = environment(), input = env.addInput('1405/07/05');
    env.run(); env.click(input);
    env.action(input._jalaliPicker, { day: '12' });
    assert.equal(input.value, '1405/07/12');
    assert.ok(input.events.includes('input') && input.events.includes('change'));
    assert.equal(input._jalaliPicker.popup.classList.contains('is-open'), false);
});

test('first click also works for a field mounted after the script', () => {
    const env = environment(); env.run();
    const input = env.addInput('1405/01/01'); env.click(input);
    assert.equal(input._jalaliPicker.popup.classList.contains('is-open'), true);
    assert.match(input._jalaliPicker.popup.innerHTML, /فروردین/);
});

test('month navigation rolls the year, today selects, and clear empties the input', () => {
    const env = environment(), input = env.addInput('1404/12/29');
    env.run(); env.click(input);
    env.action(input._jalaliPicker, { action: 'next' });
    assert.equal(input._jalaliPicker.view[0], 1405);
    assert.equal(input._jalaliPicker.view[1], 1);
    env.action(input._jalaliPicker, { action: 'prev' });
    assert.equal(input._jalaliPicker.view[0], 1404);
    env.action(input._jalaliPicker, { action: 'today' });
    assert.match(input.value, /^\d{4}\/\d{2}\/\d{2}$/);
    env.click(input);
    env.action(input._jalaliPicker, { action: 'clear' });
    assert.equal(input.value, '');
});

test('Persian digits work and repeated asset execution does not create duplicate pickers', () => {
    const env = environment(), input = env.addInput('۱۴۰۵/۰۷/۰۵');
    env.run(); env.run(); env.click(input);
    assert.equal(input._jalaliPicker.popup.classList.contains('is-open'), true);
    assert.equal(env.nodes.filter(node => node.classList.contains('admin-jalali-picker')).length, 1);
});

test('Esfand leap-year days agree with the server calendar', () => {
    const env = environment(), input = env.addInput('1403/12/01');
    env.run(); env.click(input);
    assert.equal((input._jalaliPicker.popup.innerHTML.match(/data-day=/g) || []).length, 30);
    input._jalaliPicker.close(); input.value = '1404/12/01'; env.click(input);
    assert.equal((input._jalaliPicker.popup.innerHTML.match(/data-day=/g) || []).length, 29);
});

const rangeField = role => ({ jalaliRange: 'test-range', jalaliRole: role, jalaliTime: 'true' });

test('earlier end days cannot be committed but browsing earlier months and years stays available', () => {
    const env = environment();
    env.addInput('1405/07/05 14:30', rangeField('from'));
    const until = env.addInput('', rangeField('until'));
    env.run(); env.click(until);
    const picker = until._jalaliPicker;
    assert.match(picker.popup.innerHTML, /data-day="4"[^>]*disabled/);
    assert.doesNotMatch(picker.popup.innerHTML, /data-day="5"[^>]*disabled/);
    env.action(picker, { day: '4' }); env.action(picker, { action: 'confirm' });
    assert.equal(until.value, '');
    env.action(picker, { action: 'month' });
    assert.doesNotMatch(picker.popup.innerHTML, /data-month="6"[^>]*disabled/);
    env.action(picker, { action: 'year' });
    assert.doesNotMatch(picker.popup.innerHTML, /data-year="1404"[^>]*disabled/);
    env.action(picker, { year: '1404' });
    env.action(picker, { month: '6' });
    assert.equal(picker.view[0], 1404);
    assert.equal(picker.view[1], 6);
    assert.match(picker.popup.innerHTML, /data-day="1"[^>]*disabled/);
    assert.equal(picker.canNavigate(-1), true);
});

test('year selection leads to months then days and commits only on confirmation', () => {
    const env = environment(), input = env.addInput('1405/07/05', rangeField('from'));
    env.run(); env.click(input);
    const picker = input._jalaliPicker;
    env.action(picker, { action: 'year' });
    assert.equal(picker.mode, 'years');
    env.action(picker, { year: '1404' });
    assert.equal(picker.mode, 'months');
    assert.equal((picker.popup.innerHTML.match(/data-month=/g) || []).length, 12);
    env.action(picker, { month: '2' });
    assert.equal(picker.mode, 'days');
    env.action(picker, { day: '10' });
    assert.equal(input.value, '1405/07/05');
    env.action(picker, { action: 'confirm' });
    assert.equal(input.value, '1404/02/10');
});

test('same-day hour and minute limits are enforced and equal start time is allowed', () => {
    const env = environment();
    env.addInput('۱۴۰۵/۰۷/۰۵ ۱۴:۳۰', rangeField('from'));
    const until = env.addInput('', rangeField('until'));
    env.run(); env.click(until);
    const picker = until._jalaliPicker;
    env.action(picker, { day: '5' });
    picker.changeTime({ target: { dataset: { time: 'enabled' }, checked: true } });
    picker.changeTime({ target: { dataset: { time: 'hour' }, value: '14' } });
    assert.match(picker.popup.innerHTML, /value="13"[^>]*disabled/);
    picker.changeTime({ target: { dataset: { time: 'minute' }, value: '29' } });
    assert.equal(picker.minute, 30);
    env.action(picker, { action: 'confirm' });
    assert.equal(until.value, '1405/07/05 14:30');
});

test('moving start forward clears an invalid end and removing start lifts restrictions', () => {
    const env = environment();
    const from = env.addInput('1405/07/05 10:00', rangeField('from'));
    const until = env.addInput('1405/07/05 11:00', rangeField('until'));
    env.run();
    from._jalaliPicker.write('1405/07/05 12:00');
    assert.equal(until.value, '');
    assert.ok(until.events.includes('input'));
    from._jalaliPicker.write(''); env.click(until);
    assert.equal(until._jalaliPicker.allowed([1400, 1, 1]), true);
});

test('date-only end on same day includes the whole day even with a timed start', () => {
    const env = environment();
    const from = env.addInput('1405/07/05 23:59', rangeField('from'));
    const until = env.addInput('1405/07/05', rangeField('until'));
    env.run(); from._jalaliPicker.syncRange();
    assert.equal(until.value, '1405/07/05');
    env.click(until); env.action(until._jalaliPicker, { day: '5' }); env.action(until._jalaliPicker, { action: 'confirm' });
    assert.equal(until.value, '1405/07/05');
});

test('year pages navigate in groups and the selected year opens its month grid', () => {
    const env = environment(), input = env.addInput('1405/01/01');
    env.run(); env.click(input);
    const picker = input._jalaliPicker;
    env.action(picker, { action: 'year' });
    const initial = picker.yearPage;
    env.action(picker, { action: 'prev' });
    assert.equal(picker.yearPage, initial - 12);
    env.action(picker, { year: String(initial - 1) });
    assert.equal(picker.mode, 'months');
});

test('bubbling clicks keep the calendar open across year, month and day rerenders', () => {
    const env = environment(), input = env.addInput('1405/07/05', rangeField('from'));
    env.run(); env.click(input);
    const picker = input._jalaliPicker;
    for (const action of [{ action: 'year' }, { year: '1403' }, { month: '2' }, { day: '12' }]) {
        env.action(picker, action);
        assert.equal(picker.popup.classList.contains('is-open'), true, JSON.stringify(action));
    }
    env.action(picker, { action: 'confirm' });
    assert.equal(input.value, '1403/02/12');
    assert.equal(picker.popup.classList.contains('is-open'), false);
});

test('publication picker always includes hours and minutes in the committed value', () => {
    const env = environment(), input = env.addInput('', { jalaliTime: 'true', jalaliTimeRequired: 'true' });
    env.run(); env.click(input);
    const picker = input._jalaliPicker;
    assert.equal(picker.includeTime, true);
    picker.changeTime({ target: { dataset: { time: 'enabled' }, checked: false } });
    assert.equal(picker.includeTime, true);
    env.action(picker, { day: '10' });
    picker.changeTime({ target: { dataset: { time: 'hour' }, value: '14' } });
    picker.changeTime({ target: { dataset: { time: 'minute' }, value: '30' } });
    env.action(picker, { action: 'confirm' });
    assert.match(input.value, /^\d{4}\/\d{2}\/10 14:30$/);
});
