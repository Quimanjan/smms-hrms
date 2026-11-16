<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Direct messages between employees, HR, Accounting and Admin.
 * One conversation per pair of people. The page checks for new messages
 * every few seconds, so replies show up without reloading.
 */
class MessageController extends Controller
{
    private const STAFF_ROLES = ['Admin', 'HR', 'Accounting', 'Employee'];

    public function index(Request $request): View
    {
        return view('messages', [
            'people' => $this->peopleFor($request->user()),
            'startWith' => $request->integer('to') ?: null,
        ]);
    }

    /** Everyone the signed-in person can message (for the chat window's search). */
    public function people(Request $request): JsonResponse
    {
        return response()->json(['people' => $this->peopleFor($request->user())]);
    }

    /** Conversation list with the last message and unread count. */
    public function conversations(Request $request): JsonResponse
    {
        $me = $request->user()->id;

        $rows = DB::table('conversation_participants as mine')
            ->join('conversations as c', 'c.id', '=', 'mine.conversation_id')
            ->join('conversation_participants as other', fn ($j) => $j->on('other.conversation_id', '=', 'c.id')->where('other.user_id', '!=', $me))
            ->join('users as u', 'u.id', '=', 'other.user_id')
            ->where('mine.user_id', $me)
            ->orderByDesc('c.last_message_at')
            ->get(['c.id', 'c.last_message_at', 'mine.last_read_message_id', 'u.id as user_id', 'u.name']);

        $list = $rows->map(function ($r) use ($me) {
            $last = DB::table('messages')->where('conversation_id', $r->id)->orderByDesc('id')->first(['id', 'body', 'user_id', 'created_at']);
            $unread = DB::table('messages')->where('conversation_id', $r->id)
                ->where('id', '>', $r->last_read_message_id)->where('user_id', '!=', $me)->count();

            return [
                'id' => $r->id,
                'user_id' => $r->user_id,
                'name' => $r->name,
                'role' => User::find($r->user_id)?->getRoleNames()->first(),
                'last' => $last ? (($last->user_id === $me ? 'You: ' : '').mb_strimwidth($last->body, 0, 70, '...')) : '',
                'when' => $last ? Carbon::parse($last->created_at)->timezone(config('app.display_timezone'))->format('M j, g:i A') : '',
                'unread' => $unread,
            ];
        });

        return response()->json(['conversations' => $list, 'unread' => $list->sum('unread')]);
    }

    /** Messages in one conversation (optionally only those after an id). Marks them read. */
    public function show(Request $request, int $conversation): JsonResponse
    {
        $me = $request->user()->id;
        $this->authorizeParticipant($conversation, $me);

        $after = max(0, $request->integer('after'));
        $messages = DB::table('messages')
            ->where('conversation_id', $conversation)
            ->where('id', '>', $after)
            ->orderBy('id')
            ->limit(500)
            ->get(['id', 'user_id', 'body', 'created_at'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'mine' => (int) $m->user_id === (int) $me,
                'body' => $m->body,
                'when' => Carbon::parse($m->created_at)->timezone(config('app.display_timezone'))->format('M j, g:i A'),
            ]);

        if ($messages->isNotEmpty()) {
            DB::table('conversation_participants')
                ->where('conversation_id', $conversation)->where('user_id', $me)
                ->update(['last_read_message_id' => $messages->last()['id'], 'updated_at' => now()]);
        }

        return response()->json(['messages' => $messages]);
    }

    /** Send a message to a person (starts the conversation if needed). */
    public function store(Request $request): JsonResponse
    {
        $me = $request->user();
        $data = $request->validate([
            'to' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $to = User::findOrFail($data['to']);
        abort_if($to->id === $me->id, 422, 'You cannot message yourself.');
        abort_unless($to->hasAnyRole(self::STAFF_ROLES), 422, 'You can only message school staff.');

        $conversation = $this->conversationBetween($me->id, $to->id);

        $id = DB::table('messages')->insertGetId([
            'conversation_id' => $conversation,
            'user_id' => $me->id,
            'body' => trim($data['body']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('conversations')->where('id', $conversation)->update(['last_message_at' => now(), 'updated_at' => now()]);
        DB::table('conversation_participants')->where('conversation_id', $conversation)->where('user_id', $me->id)
            ->update(['last_read_message_id' => $id]);

        return response()->json(['conversation' => $conversation, 'id' => $id], 201);
    }

    /** Unread count for the menu badge. */
    public static function unreadFor(int $userId): int
    {
        try {
            return (int) DB::table('messages as m')
                ->join('conversation_participants as p', fn ($j) => $j->on('p.conversation_id', '=', 'm.conversation_id')->where('p.user_id', $userId))
                ->whereColumn('m.id', '>', 'p.last_read_message_id')
                ->where('m.user_id', '!=', $userId)
                ->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    private function peopleFor(User $me): array
    {
        return User::role(self::STAFF_ROLES)
            ->where('id', '!=', $me->id)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $u) => ['id' => $u->id, 'name' => $u->name, 'role' => $u->getRoleNames()->first()])
            ->values()
            ->all();
    }

    private function conversationBetween(int $a, int $b): int
    {
        $existing = DB::table('conversation_participants as x')
            ->join('conversation_participants as y', 'y.conversation_id', '=', 'x.conversation_id')
            ->where('x.user_id', $a)->where('y.user_id', $b)
            ->value('x.conversation_id');

        if ($existing) {
            return (int) $existing;
        }

        return DB::transaction(function () use ($a, $b) {
            $id = DB::table('conversations')->insertGetId(['created_at' => now(), 'updated_at' => now(), 'last_message_at' => now()]);
            DB::table('conversation_participants')->insert([
                ['conversation_id' => $id, 'user_id' => $a, 'last_read_message_id' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['conversation_id' => $id, 'user_id' => $b, 'last_read_message_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ]);

            return $id;
        });
    }

    private function authorizeParticipant(int $conversation, int $userId): void
    {
        abort_unless(
            DB::table('conversation_participants')->where('conversation_id', $conversation)->where('user_id', $userId)->exists(),
            404
        );
    }
}
