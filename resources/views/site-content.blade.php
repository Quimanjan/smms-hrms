<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold leading-tight text-red-700 sm:text-3xl md:text-4xl">Homepage</h2>
    </x-slot>

    <div class="mx-auto max-w-[1100px] px-4 pb-12 pt-2 sm:px-6 lg:px-10">
        <form method="POST" action="{{ route('site-content.update') }}" class="cms">
            @csrf
            @method('PUT')

            <div class="cms-intro">
                <p>Change the words on the public homepage. Saving updates the site right away. Leave a box empty to go back to the original text.</p>
                <div class="cms-links">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">Open the homepage</a>
                    <a href="{{ route('dashboard') }}">Change the announcement banners on the dashboard</a>
                </div>
            </div>

            @if (session('success'))
                <p class="rq-flash" role="status">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <p class="rq-flash rq-flash--error" role="alert">{{ $errors->first() }}</p>
            @endif

            @foreach (collect($fields)->groupBy(fn ($f) => $f[3], true) as $group => $items)
                <fieldset class="cms-group">
                    <legend>{{ $group }}</legend>
                    @foreach ($items as $key => [$label, $default, $rows])
                        <label class="cms-field">
                            <span>{{ $label }}</span>
                            @if ($rows > 1)
                                <textarea name="{{ $key }}" rows="{{ $rows }}">{{ old($key, $values[$key]) }}</textarea>
                            @else
                                <input type="{{ $key === 'email' ? 'email' : ($key === 'facebook' ? 'url' : 'text') }}" name="{{ $key }}" value="{{ old($key, $values[$key]) }}">
                            @endif
                            @error($key) <small class="cms-error">{{ $message }}</small> @enderror
                        </label>
                    @endforeach
                </fieldset>
            @endforeach

            <div class="cms-actions">
                <button type="submit">Save homepage</button>
            </div>
        </form>
    </div>
</x-app-layout>
