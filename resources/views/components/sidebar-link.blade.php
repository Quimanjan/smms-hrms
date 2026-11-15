{{-- resources/views/components/sidebar-link.blade.php --}}
@props(['active' => false, 'icon' => 'fa-circle'])

@php
    // Colors come from partials/app-theme.blade.php.
    $classes = $active
        ? 'app-side-link is-active ml-3 mr-3'
        : 'app-side-link ml-3 mr-3';
@endphp

<a {{ $attributes->merge(['class' => "flex items-center gap-4 px-6 py-3 text-sm font-medium $classes"]) }} title="{{ trim(strip_tags($slot)) }}" @if ($active) aria-current="page" @endif>
    <i class="fa-solid {{ $icon }} w-4 text-center"></i>
    <span>{{ $slot }}</span>
</a>