{{-- Site footer shared by the public pages. --}}
<footer class="mg-foot">
    <div class="mg-wrap mg-foot-grid">
        <div class="mg-foot-brand">
            <img src="{{ asset('image/SMMS Logo.png') }}" alt="" width="60" height="60">
            <div>
                <strong>St. Mary Magdalene School</strong>
                <span>(Parochial), Inc.</span>
                <span>Poblacion, Kawit, Cavite</span>
            </div>
        </div>

        <ul class="mg-contact">
            <li>
                <a href="tel:+639672399400">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h3l1.5 4.5-2.2 1.5a11 11 0 0 0 5.7 5.7l1.5-2.2L21 14v3a2 2 0 0 1-2 2C10.7 19 5 13.3 5 5a2 2 0 0 1 2-2z"/></svg>
                    <span>(+63) 967-239-9400</span>
                </a>
            </li>
            <li>
                <a href="mailto:communications@smms.ph.education">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <span>communications@smms.ph.education</span>
                </a>
            </li>
            <li>
                <a href="https://www.google.com/maps/search/?api=1&query=Diocesan+Shrine+and+Parish+of+St.+Mary+Magdalene+Kawit+Cavite" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.800 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    <span>Find us on Google Maps</span>
                </a>
            </li>
            <li>
                <a href="https://www.facebook.com/SMMSchool1963" target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.500 21v-7h2.400l.400-3h-2.800V9.200c0-.900.300-1.500 1.500-1.500h1.400V5.100A18 18 0 0 0 14.300 5c-2.100 0-3.500 1.300-3.500 3.600V11H8.400v3h2.400v7z"/></svg>
                    <span>St. Mary Magdalene School on Facebook</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="mg-wrap mg-foot-legal">
        &copy; {{ now()->year }} St. Mary Magdalene School (Parochial), Inc.
    </div>
</footer>
