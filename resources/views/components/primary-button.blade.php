<button {{ $attributes->merge(['type' => 'submit', 'class' => 'hb hb--primary']) }}>
    {{ $slot }}
</button>
