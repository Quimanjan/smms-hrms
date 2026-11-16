<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Admin: the audit trail of user activity. */
class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:30'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $logs = $this->query($filters)->paginate(25)->withQueryString();

        return view('activity-log', [
            'logs' => $logs,
            'filters' => $filters,
            'actions' => ['created', 'updated', 'deleted', 'signed in', 'signed out'],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:30'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $rows = $this->query($filters)->limit(5000)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date and time', 'User', 'Role', 'Action', 'Record', 'Details', 'IP address']);
            foreach ($rows as $r) {
                fputcsv($out, [\Carbon\Carbon::parse($r->created_at)->timezone(config('app.display_timezone'))->format('Y-m-d H:i:s'), $r->user_name, $r->role, $r->action, $r->description, $this->changesText($r->changes), $r->ip]);
            }
            fclose($out);
        }, 'audit-trail-'.now(config('app.display_timezone'))->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function query(array $f)
    {
        return DB::table('activity_logs')
            ->when($f['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('user_name', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")))
            ->when($f['action'] ?? null, fn ($q, $a) => $q->where('action', $a))
            ->when($f['from'] ?? null, fn ($q, $d) => $q->where('created_at', '>=', \Carbon\Carbon::parse($d, config('app.display_timezone'))->startOfDay()->utc()))
            ->when($f['to'] ?? null, fn ($q, $d) => $q->where('created_at', '<=', \Carbon\Carbon::parse($d, config('app.display_timezone'))->endOfDay()->utc()))
            ->orderByDesc('id');
    }

    public static function changesText(?string $json): string
    {
        $changes = $json ? json_decode($json, true) : null;
        if (! $changes) {
            return '';
        }

        return collect($changes)
            ->map(fn ($c, $field) => str_replace('_', ' ', $field).': '.($c['from'] ?? 'empty').' to '.($c['to'] ?? 'empty'))
            ->implode('; ');
    }
}
