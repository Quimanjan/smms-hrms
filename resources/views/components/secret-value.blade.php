{{--
    A sensitive number (government ID) that stays hidden until the viewer
    clicks the eye button. Shows only the last 4 characters while hidden.

    Usage: <x-secret-value :value="$employee->sss_number" label="SSS Number" />
--}}
@props(['value' => null, 'label' => 'number'])

@php
    $value = trim((string) $value);
    $tail = substr(preg_replace('/\D+/', '', $value), -4);
    $masked = $value === '' ? '' : '•••• •••• '.$tail;
@endphp

@if ($value === '')
    <span {{ $attributes->merge(['class' => 'italic text-gray-400']) }}>Not provided</span>
@else
    <span x-data="{ shown: false }" {{ $attributes->merge(['class' => 'secret-value']) }}>
        <span class="secret-value__text" x-text="shown ? @js($value) : @js($masked)">{{ $masked }}</span>
        <button type="button" class="secret-value__eye" @click.prevent="shown = !shown"
            :aria-label="(shown ? 'Hide ' : 'Show ') + @js($label)" :aria-pressed="shown.toString()"
            :title="shown ? 'Hide' : 'Show'">
            <svg x-show="!shown" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg x-show="shown" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.1M6.6 6.6C3.9 8.4 2 12 2 12s3.6 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
        </button>
    </span>
@endif
