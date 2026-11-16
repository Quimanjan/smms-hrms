{{--
    The two system fonts on their own, for pages that do not load the full theme:
    Marcellus for headings, Instrument Sans for everything else.
--}}
<link rel="preload" href="{{ asset('fonts/instrument-sans-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
<style>
    @font-face { font-family: 'Marcellus'; src: url("{{ asset('fonts/marcellus-400.woff2') }}") format('woff2'); font-weight: 400; font-style: normal; font-display: swap; }
    @font-face { font-family: 'Instrument Sans'; src: url("{{ asset('fonts/instrument-sans-variable.woff2') }}") format('woff2'); font-weight: 400 700; font-style: normal; font-display: swap; }
    html, body, .font-sans { font-family: 'Instrument Sans', system-ui, 'Segoe UI', Roboto, Arial, sans-serif; }
    button, input, select, textarea, optgroup { font-family: inherit; }
    h1, h2 { font-family: 'Marcellus', 'Times New Roman', serif; font-weight: 400; }

/* ---------- No default blue: checkboxes, radios and focus rings use the brand red (client revision) ---------- */
input[type="checkbox"], input[type="radio"] { accent-color: var(--brand-red, #b22222); color: var(--brand-red, #b22222); }
input[type="checkbox"]:focus, input[type="radio"]:focus,
input:not([type="checkbox"]):not([type="radio"]):focus, select:focus, textarea:focus { --tw-ring-color: rgba(178, 34, 34, .18); }
input:not([type="checkbox"]):not([type="radio"]):focus, select:focus, textarea:focus { border-color: var(--brand-red, #b22222); }
.focus\:ring-indigo-500:focus, .focus\:border-indigo-500:focus, .focus\:border-indigo-700:focus, .focus\:border-blue-300:focus, .focus\:border-blue-700:focus { --tw-ring-color: rgba(178, 34, 34, .18); border-color: var(--brand-red, #b22222); }
.border-indigo-400 { border-color: var(--brand-red, #b22222) !important; }
.text-indigo-700, .focus\:text-indigo-800:focus { color: var(--brand-red, #b22222) !important; }
.bg-indigo-50, .focus\:bg-indigo-100:focus { background-color: #fdf2f2 !important; }
</style>
