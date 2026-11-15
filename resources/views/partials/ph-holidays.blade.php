{{-- Philippine holidays and school days, shared by the dashboard calendars. --}}
<script>
window.smmsHolidayOn = window.smmsHolidayOn || (function () {
    const key = (d) => d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
    function easter(year) {
        const a = year % 19, b = Math.floor(year / 100), c = year % 100, d = Math.floor(b / 4), e = b % 4;
        const f = Math.floor((b + 8) / 25), g = Math.floor((b - f + 1) / 3), h = (19 * a + b - d - g + 15) % 30;
        const i = Math.floor(c / 4), k = c % 4, l = (32 + 2 * e + 2 * i - h - k) % 7, m = Math.floor((a + 11 * h + 22 * l) / 451);
        const month = Math.floor((h + l - 7 * m + 114) / 31), day = ((h + l - 7 * m + 114) % 31) + 1;
        return new Date(year, month - 1, day);
    }
    const cache = {};
    function forYear(year) {
        if (cache[year]) return cache[year];
        const list = {};
        const add = (date, name, type) => { list[key(date)] = { name, type }; };
        const on = (m, d, name, type) => add(new Date(year, m - 1, d), name, type);
        on(1, 1, "New Year's Day", 'regular');
        on(4, 9, 'Araw ng Kagitingan', 'regular');
        on(5, 1, 'Labor Day', 'regular');
        on(6, 12, 'Independence Day', 'regular');
        on(11, 30, 'Bonifacio Day', 'regular');
        on(12, 25, 'Christmas Day', 'regular');
        on(12, 30, 'Rizal Day', 'regular');
        on(8, 21, 'Ninoy Aquino Day', 'special');
        on(11, 1, "All Saints' Day", 'special');
        on(11, 2, "All Souls' Day", 'special');
        on(12, 8, 'Feast of the Immaculate Conception', 'special');
        on(12, 24, 'Christmas Eve', 'special');
        on(12, 31, 'Last Day of the Year', 'special');
        on(3, 11, 'School Foundation Day', 'school');
        on(7, 22, 'Feast of St. Mary Magdalene', 'school');
        const e = easter(year);
        const shift = (n) => new Date(e.getFullYear(), e.getMonth(), e.getDate() + n);
        add(shift(-3), 'Maundy Thursday', 'regular');
        add(shift(-2), 'Good Friday', 'regular');
        add(shift(-1), 'Black Saturday', 'special');
        const heroes = new Date(year, 8, 0);
        while (heroes.getDay() !== 1) heroes.setDate(heroes.getDate() - 1);
        add(heroes, 'National Heroes Day', 'regular');
        return (cache[year] = list);
    }
    return (date) => forYear(date.getFullYear())[key(date)] || null;
})();
</script>
