@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'hrms-input']) }}>
