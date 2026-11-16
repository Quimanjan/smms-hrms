<x-app-layout>
    <x-slot name="title">Admin Audit Trail</x-slot>
    <x-slot name="header">
        <h2 class="text-2xl font-extrabold leading-tight text-red-700 sm:text-3xl md:text-4xl">Audit Trail</h2>
    </x-slot>

    <div class="mx-auto max-w-[1920px] px-4 pb-12 pt-2 sm:px-6 lg:px-10">
        <div class="rm">
            <form method="GET" action="{{ route('activity-log.index') }}" class="rm-bar">
                <label for="al-q">Search</label>
                <input id="al-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name or record">
                <label for="al-action" class="rm-bar-gap">Action</label>
                <select id="al-action" name="action">
                    <option value="">All</option>
                    @foreach ($actions as $a)
                        <option value="{{ $a }}" @selected(($filters['action'] ?? '') === $a)>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
                <label for="al-from" class="rm-bar-gap">From</label>
                <input id="al-from" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
                <label for="al-to">To</label>
                <input id="al-to" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
                <button type="submit" class="rm-export" style="margin-left: 8px">Filter</button>
                <a class="rm-export al-dl" href="{{ route('activity-log.export', request()->query()) }}">Download CSV</a>
            </form>

            <div class="rm-card">
                <header class="rm-card-head">
                    <h3>User activity</h3>
                    <p>Every change made by a signed-in person, plus sign-ins and sign-outs. Passwords and government ID numbers are never shown.</p>
                </header>

                @if ($logs->isEmpty())
                    <p class="rm-empty">No activity matches these filters.</p>
                @else
                    <div class="rm-wrap">
                        <table class="rm-table al-table">
                            <thead>
                                <tr>
                                    <th>Date and time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Record</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</td>
                                        <td><strong>{{ $log->user_name ?? 'Unknown' }}</strong><small>{{ $log->role }}</small></td>
                                        <td><span class="al-action al-action--{{ str_replace(' ', '-', $log->action) }}">{{ ucfirst($log->action) }}</span></td>
                                        <td>{{ $log->description }}</td>
                                        <td class="al-changes">{{ \App\Http\Controllers\ActivityLogController::changesText($log->changes) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="al-pages">{{ $logs->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
