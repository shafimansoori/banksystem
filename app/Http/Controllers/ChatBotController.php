<?php

namespace App\Http\Controllers;

use App\Services\ChatBot\ChatBotService;
use App\Models\ChatConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatBotController extends Controller
{
    public function __construct(
        protected ChatBotService $chatBotService
    ) {
    }

    public function respond(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'session_id' => ['nullable', 'string', 'max:100'],
        ]);

        $payload = $this->chatBotService->respond(
            $validated['message'],
            $validated['session_id'] ?? null
        );

        return response()->json([
            'message' => $payload['response'],
            'score' => $payload['score'],
            'intent' => $payload['intent'],
            'sentiment' => $payload['sentiment'] ?? 'neutral',
            'entities' => $payload['entities'] ?? [],
            'session_id' => $payload['session_id'],
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'user_message' => $validated['message'],
            'used_ai' => $payload['used_ai'] ?? true,
        ]);
    }

    /**
     * Get chat history for the current user
     */
    public function getHistory(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['messages' => []]);
        }

        $limit = $request->input('limit', 50);

        $conversations = ChatConversation::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        $messages = [];
        foreach ($conversations as $conv) {
            // User message
            $messages[] = [
                'type' => 'user',
                'text' => $conv->user_message,
                'time' => $conv->created_at->format('H:i'),
            ];
            // Bot response
            $messages[] = [
                'type' => 'bot',
                'text' => $conv->bot_response,
                'time' => $conv->created_at->format('H:i'),
            ];
        }

        return response()->json([
            'messages' => $messages,
            'count' => count($messages),
        ]);
    }

    /**
     * Clear chat history for the current user
     */
    public function clearHistory(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        ChatConversation::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true, 'message' => 'Geçmiş temizlendi']);
    }
}


