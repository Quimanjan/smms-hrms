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
</style>
