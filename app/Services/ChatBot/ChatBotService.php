<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;

class ChatBotService
{
    protected OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Generate a response for the provided message.
     */
    public function respond(string $message, ?string $sessionId = null): array
    {
        // Check if OpenAI is configured
        if (!$this->openAIService->isConfigured()) {
            throw new \Exception('OpenAI API anahtarı yapılandırılmamış. Lütfen .env dosyasında OPENAI_API_KEY değerini ayarlayın.');
        }

        $sessionId = $sessionId ?? $this->generateSessionId();
        $context = $this->getConversationContext($sessionId);

        $openAIContext = [
            'conversation_history' => $context['conversation_flow'] ?? [],
        ];

        $openAIResult = $this->openAIService->generateResponse($message, $openAIContext);

        if (!$openAIResult || empty($openAIResult['response'])) {
            throw new \Exception('AI yanıt üretemedi. Lütfen tekrar deneyin.');
        }

        $result = [
            'response' => $openAIResult['response'],
            'score' => 0.95,
            'intent' => 'openai_response',
            'sentiment' => 'neutral',
            'entities' => [],
            'session_id' => $sessionId,
            'used_ai' => true,
        ];

        $this->saveConversation($message, $result, $sessionId, $context);

        return $result;
    }

    /**
     * Generate unique session ID
     */
    protected function generateSessionId(): string
    {
        return 'session_' . Str::random(32) . '_' . time();
    }

    /**
     * Get conversation context
     */
    protected function getConversationContext(string $sessionId): array
    {
        $recentConversations = ChatConversation::forSession($sessionId)
            ->recent(5)
            ->get()
            ->reverse();

        $context = [
            'conversation_flow' => [],
        ];

        foreach ($recentConversations as $conversation) {
            $context['conversation_flow'][] = [
                'user_message' => $conversation->user_message,
                'bot_response' => $conversation->bot_response,
            ];
        }

        return $context;
    }

    /**
     * Save conversation to database
     */
    protected function saveConversation(string $userMessage, array $response, string $sessionId, array $context): void
    {
        try {
            ChatConversation::create([
                'user_id' => Auth::id(),
                'session_id' => $sessionId,
                'user_message' => $userMessage,
                'bot_response' => $response['response'],
                'intent' => $response['intent'],
                'sentiment' => $response['sentiment'],
                'confidence' => $response['score'],
                'entities' => $response['entities'],
                'context' => $context,
                'language' => 'tr',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save chat conversation: ' . $e->getMessage());
        }
    }
}
