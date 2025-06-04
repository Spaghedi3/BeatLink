<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChMessage;
use App\Models\User;

class AdminConversationController extends Controller
{

    public function show($reportedId, $reporterId)
    {
        $reported = User::findOrFail($reportedId);
        $reporter = User::findOrFail($reporterId);

        // Only messages between the two users (both directions)
        $messages = ChMessage::where(function ($query) use ($reportedId, $reporterId) {
            $query->where('from_id', $reportedId)->where('to_id', $reporterId);
        })
            ->orWhere(function ($query) use ($reportedId, $reporterId) {
                $query->where('from_id', $reporterId)->where('to_id', $reportedId);
            })
            ->orderBy('created_at')
            ->get();

        return view('admin.conversations.chatify', compact('reported', 'reporter', 'messages'));
    }
}
