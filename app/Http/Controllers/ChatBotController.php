<?php

namespace App\Http\Controllers;

use App\Services\ChatBot\ChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        ]);
    }
}


