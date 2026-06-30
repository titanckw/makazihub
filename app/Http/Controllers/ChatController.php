<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\NotificationLog;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * List of conversation partners for the logged-in user.
     */
    private function partners(): \Illuminate\Support\Collection
    {
        $user = Auth::user();

        if ($user->hasRole('manager')) {
            return Staff::where('manager_id', $user->id)->with('user')->get()->pluck('user');
        }

        // staff -> only their manager
        $staff = Staff::where('user_id', $user->id)->first();
        return $staff ? collect([$staff->manager]) : collect();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $partners = $this->partners();

        $partnerId = $request->integer('with') ?: optional($partners->first())->id;
        $activePartner = $partnerId ? $partners->firstWhere('id', $partnerId) : null;

        $messages = collect();
        if ($activePartner) {
            $messages = ChatMessage::between($user->id, $activePartner->id)
                ->orderBy('created_at')
                ->get();

            ChatMessage::where('sender_id', $activePartner->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $unreadCounts = ChatMessage::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->selectRaw('sender_id, count(*) as total')
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        $layout = $user->hasRole('manager') ? 'manager' : 'staff';

        return view("{$layout}.chat.index", compact('partners', 'activePartner', 'messages', 'unreadCounts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message'      => 'required|string|max:2000',
        ]);

        $partners = $this->partners();
        abort_unless($partners->contains('id', (int) $validated['recipient_id']), 403);

        $message = ChatMessage::create([
            'sender_id'    => $user->id,
            'recipient_id' => $validated['recipient_id'],
            'message'      => $validated['message'],
        ]);

        NotificationLog::notify(
            $validated['recipient_id'],
            'chat_message',
            'New message from ' . $user->name,
            \Illuminate\Support\Str::limit($validated['message'], 80)
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => $message->load('sender')]);
        }

        return back();
    }

    /**
     * Polled by JS to fetch new messages since a given message id / timestamp.
     */
    public function poll(Request $request)
    {
        $user = Auth::user();
        $partnerId = $request->integer('with');
        $afterId = $request->integer('after_id', 0);

        $messages = ChatMessage::between($user->id, $partnerId)
            ->where('id', '>', $afterId)
            ->orderBy('created_at')
            ->get(['id', 'sender_id', 'recipient_id', 'message', 'created_at']);

        ChatMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }
}
