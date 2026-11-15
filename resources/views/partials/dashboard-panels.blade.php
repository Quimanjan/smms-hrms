{{-- "Our people" and "Workforce" panels on the staff dashboards. --}}
@php
    $team = \App\Support\DashboardOverview::team();
    $peopleUrl = auth()->user()->hasAnyRole(['Admin', 'HR']) && Route::has('employees.index') ? route('employees.index') : null;
    $palette = ['#a5121e', '#c9a24b', '#4a0a12', '#d98f6f', '#6d6164'];
    $circumference = 2 * M_PI * 46;
    $offset = 0;
@endphp

@if ($team['total'] > 0)
<div class="dp">
    <section class="dp-card dp-people" aria-labelledby="dp-people-title">
        <div class="dp-head">
            <h2 id="dp-people-title" class="dp-title">Our people</h2>
            @if ($peopleUrl)
                <a class="dp-link" href="{{ $peopleUrl }}">See everyone</a>
            @endif
        </div>
        <p class="dp-sub">{{ $team['total'] }} {{ $team['total'] === 1 ? 'person works' : 'people work' }} at St. Mary Magdalene School.</p>

        <ul class="dp-faces">
            @foreach ($team['people']->take(10) as $person)
                <li style="--i: {{ $loop->index }}">
                    <span class="av-host dp-face" title="{{ $person->name }}, {{ $person->position }}">
                        {!! \App\Support\Avatar::for($person, $person->name.', '.$person->position) !!}
                    </span>
                    <span class="dp-face-name">{{ \Illuminate\Support\Str::of($person->name)->explode(' ')->first() }}</span>
                    <span class="dp-face-role">{{ $person->position }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="dp-card dp-mix" aria-labelledby="dp-mix-title">
        <div class="dp-head">
            <h2 id="dp-mix-title" class="dp-title">Workforce</h2>
        </div>

        <div class="dp-mix-body">
            <svg class="dp-donut" viewBox="0 0 120 120" role="img" aria-label="Employees by role type">
                <circle cx="60" cy="60" r="46" fill="none" stroke="#f1e9e7" stroke-width="14"/>
                @foreach ($team['groups'] as $i => $group)
                    @php($len = $circumference * $group['count'] / $team['total'])
                    <circle class="dp-arc" cx="60" cy="60" r="46" fill="none"
                        stroke="{{ $palette[$i % count($palette)] }}" stroke-width="14"
                        stroke-dasharray="{{ round($len, 2) }} {{ round($circumference, 2) }}"
                        stroke-dashoffset="{{ round(-$offset, 2) }}"
                        style="--len: {{ round($len, 2) }}; --i: {{ $i }}"
                        transform="rotate(-90 60 60)"/>
                    @php($offset += $len)
                @endforeach
                <text x="60" y="58" text-anchor="middle" class="dp-donut-num">{{ $team['total'] }}</text>
                <text x="60" y="74" text-anchor="middle" class="dp-donut-label">employees</text>
            </svg>

            <ul class="dp-legend">
                @foreach ($team['groups'] as $i => $group)
                    <li>
                        <span class="dp-swatch" style="background: {{ $palette[$i % count($palette)] }}"></span>
                        <span class="dp-legend-label">{{ $group['label'] }}</span>
                        <strong>{{ $group['count'] }}</strong>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="dp-split" aria-label="Employees by gender">
            <div class="dp-bar">
                @foreach ($team['genders'] as $g)
                    <span class="dp-bar-seg dp-bar-seg--{{ $g['key'] }}" style="--w: {{ round(100 * $g['count'] / $team['total'], 2) }}%; --i: {{ $loop->index }}"></span>
                @endforeach
            </div>
            <ul class="dp-split-legend">
                @foreach ($team['genders'] as $g)
                    <li><span class="dp-swatch dp-bar-seg--{{ $g['key'] }}"></span>{{ $g['label'] }} <strong>{{ $g['count'] }}</strong></li>
                @endforeach
            </ul>
        </div>
    </section>
</div>
@endif
