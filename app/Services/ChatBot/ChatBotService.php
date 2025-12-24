<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;

class ChatBotService
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $intents;

    /**
     * @var array<int, string>
     */
    protected array $stopWords;

    /**
     * @var AdvancedNLPService
     */
    protected AdvancedNLPService $nlpService;

    public function __construct(AdvancedNLPService $nlpService)
    {
        $this->intents = config('chatbot.intents', []);
        $this->stopWords = config('chatbot.stop_words', []);
        $this->nlpService = $nlpService;
    }

    /**
     * Generate a response for the provided message.
     */
    public function respond(string $message, ?string $sessionId = null): array
    {
        // Generate session ID if not provided
        $sessionId = $sessionId ?? $this->generateSessionId();
        
        // Get conversation context for better responses
        $context = $this->getConversationContext($sessionId);
        
        // Use advanced NLP analysis
        $analysis = $this->nlpService->detectAdvancedIntent($message, $context);
        
        $response = '';
        $confidence = 0;
        $intent = '';
        
        // Handle system knowledge intents first
        if (!empty($analysis['knowledge_intents'])) {
            foreach ($analysis['knowledge_intents'] as $knowledgeIntent) {
                if ($knowledgeIntent['confidence'] > 0.7) {
                    $response = $this->handleSystemKnowledgeIntent(
                        $knowledgeIntent['intent'], 
                        $message, 
                        $analysis
                    );
                    $confidence = $knowledgeIntent['confidence'];
                    $intent = $knowledgeIntent['intent'];
                    
                    // Save conversation with enhanced data
                    $this->saveConversation($sessionId, $message, $response, $analysis, $confidence, $intent);
                    
                    return [
                        'response' => $response,
                        'confidence' => $confidence,
                        'intent' => $intent,
                        'sentiment' => $analysis['sentiment'],
                        'entities' => $analysis['entities'] ?? [],
                        'session_id' => $sessionId
                    ];
                }
            }
        }
        
        // Check if we have high-confidence banking intents
        if (!empty($analysis['banking_intents']) && max($analysis['banking_intents']) > 0.7) {
            $response = $this->nlpService->generateContextualResponse($analysis);
            $confidence = $analysis['confidence'];
            $intent = array_keys($analysis['banking_intents'], max($analysis['banking_intents']))[0];
        } else {
            // Fallback to original intent matching for general conversation
            $normalizedTokens = $this->tokenize($message);

            $bestScore = 0;
            $bestIntent = null;

            foreach ($this->intents as $intentData) {
                $score = $this->scoreAgainstIntent($normalizedTokens, $intentData['patterns'] ?? []);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestIntent = $intentData;
                }
            }

            $minimumMatch = (float) config('chatbot.minimum_match', 0.25);

            if ($bestIntent !== null && $bestScore >= $minimumMatch) {
                $response = $this->pickResponse($bestIntent['responses'] ?? []);
                $intent = $bestIntent['tag'] ?? null;
                $confidence = $bestScore;
            } else {
                // Use contextual fallback response
                $response = $this->nlpService->generateContextualResponse($analysis);
                $intent = 'advanced_nlp_fallback';
                $confidence = $analysis['confidence'];
            }
        }

        $result = [
            'response' => $response,
            'score' => round($confidence, 3),
            'intent' => $intent,
            'sentiment' => $analysis['sentiment']['sentiment'] ?? 'neutral',
            'entities' => $analysis['entities'] ?? [],
            'session_id' => $sessionId,
        ];

        // Save conversation to database
        $this->saveConversation($message, $result, $sessionId, $context);

        return $result;
    }

    /**
     * Generate unique session ID for conversation tracking
     */
    protected function generateSessionId(): string
    {
        return 'session_' . Str::random(32) . '_' . time();
    }

    /**
     * Get conversation context from recent messages
     */
    protected function getConversationContext(string $sessionId): array
    {
        $recentConversations = ChatConversation::forSession($sessionId)
            ->recent(5)
            ->get()
            ->reverse(); // Oldest first for context

        $context = [
            'previous_intents' => [],
            'previous_entities' => [],
            'conversation_flow' => [],
            'user_preferences' => [],
        ];

        foreach ($recentConversations as $conversation) {
            if ($conversation->intent) {
                $context['previous_intents'][] = $conversation->intent;
            }
            
            if ($conversation->entities) {
                $context['previous_entities'] = array_merge($context['previous_entities'], $conversation->entities);
            }
            
            $context['conversation_flow'][] = [
                'user_message' => $conversation->user_message,
                'bot_response' => $conversation->bot_response,
                'intent' => $conversation->intent,
                'sentiment' => $conversation->sentiment,
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
                'language' => $this->detectLanguage($userMessage),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the conversation flow
            Log::error('Failed to save chat conversation: ' . $e->getMessage());
        }
    }

    /**
     * Simple language detection
     */
    protected function detectLanguage(string $text): string
    {
        // Simple heuristic: if contains Turkish characters, it's Turkish
        if (preg_match('/[ğüşöçıİĞÜŞÖÇ]/u', $text)) {
            return 'tr';
        }
        
        // Check for common English words
        $englishWords = ['the', 'and', 'you', 'that', 'was', 'for', 'are', 'with', 'his', 'they'];
        $words = explode(' ', strtolower($text));
        $englishCount = count(array_intersect($words, $englishWords));
        
        if ($englishCount > 0) {
            return 'en';
        }
        
        return 'tr'; // Default to Turkish
    }

    /**
     * Calculate the best score for the provided message tokens compared with all patterns of the intent.
     *
     * @param  array<int, string>  $messageTokens
     * @param  array<int, string>  $patterns
     */
    protected function scoreAgainstIntent(array $messageTokens, array $patterns): float
    {
        $bestPatternScore = 0.0;

        foreach ($patterns as $pattern) {
            $patternTokens = $this->tokenize($pattern);
            if (empty($patternTokens)) {
                continue;
            }

            $patternScore = $this->jaccardSimilarity($messageTokens, $patternTokens);
            if ($patternScore > $bestPatternScore) {
                $bestPatternScore = $patternScore;
            }
        }

        return $bestPatternScore;
    }

    /**
     * Tokenize a sentence into normalized tokens.
     *
     * @return array<int, string>
     */
    protected function tokenize(string $text): array
    {
        $text = Str::of($text)
            ->lower()
            ->replaceMatches('/[^a-zA-Z0-9ğüşöçıİĞÜŞÖÇ\s]+/u', ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim();

        $tokens = array_filter(explode(' ', (string) $text), static fn ($token) => $token !== '');

        if (empty($this->stopWords)) {
            return array_values(array_unique($tokens));
        }

        $filtered = array_filter($tokens, function ($token) {
            return !in_array($token, $this->stopWords, true);
        });

        return array_values(array_unique($filtered));
    }

    /**
     * Calculate the Jaccard similarity between two token sets.
     *
     * @param  array<int, string>  $tokensA
     * @param  array<int, string>  $tokensB
     */
    protected function jaccardSimilarity(array $tokensA, array $tokensB): float
    {
        if (empty($tokensA) || empty($tokensB)) {
            return 0.0;
        }

        $intersection = count(array_intersect($tokensA, $tokensB));
        $union = count(array_unique(array_merge($tokensA, $tokensB)));

        if ($union === 0) {
            return 0.0;
        }

        return $intersection / $union;
    }

    /**
     * Pick a random response from the provided list.
     *
     * @param  array<int, string>  $responses
     */
    protected function pickResponse(array $responses): string
    {
        if (empty($responses)) {
            return (string) config('chatbot.fallback_response');
        }

        return Arr::random($responses);
    }

    /**
     * Handle system knowledge intents
     */
    protected function handleSystemKnowledgeIntent(string $intent, string $message, array $analysis): string
    {
        try {
            $knowledgeService = $this->nlpService->getKnowledgeService();
            
            switch ($intent) {
                case 'system_help':
                    return $knowledgeService->generateHelpResponse($analysis['system_context'] ?? []);
                    
                case 'system_features':
                    return $this->generateFeaturesResponse($knowledgeService);
                    
                case 'system_navigation':
                    return $this->generateNavigationResponse($analysis['system_context'] ?? []);
                    
                case 'system_info':
                    $systemInfo = $knowledgeService->getSystemKnowledge();
                    return $this->formatSystemInfo($systemInfo);
                    
                case 'system_permissions':
                    return $this->generatePermissionsResponse($knowledgeService);
                    
                case 'system_statistics':
                    return $this->generateStatsResponse($knowledgeService);
                    
                default:
                    return $knowledgeService->generateHelpResponse($analysis['system_context'] ?? []);
            }
        } catch (\Exception $e) {
            Log::error('SystemKnowledge handler error: ' . $e->getMessage());
            return "Sistem bilgilerine erişirken bir hata oluştu. Daha sonra tekrar deneyin.";
        }
    }

    /**
     * Generate features response
     */
    protected function generateFeaturesResponse($knowledgeService): string
    {
        $features = [
            '💰 Hesap Yönetimi' => 'Banka hesaplarınızı görüntüleyin ve yönetin',
            '💳 Kart İşlemleri' => 'Kredi kartlarınızı kontrol edin ve işlem yapın',
            '🔄 Para Transferi' => 'Hesaplar arası ve dış hesaplara para transferi',
            '📊 İşlem Geçmişi' => 'Detaylı işlem raporları ve geçmiş',
            '🧾 Fatura Ödeme' => 'Elektrik, su, telefon faturaları ödemesi',
            '📱 Mobil Bankacılık' => 'Mobil uygulamadan tüm işlemler',
            '🔒 Güvenlik' => 'İki faktörlü doğrulama ve güvenli işlemler'
        ];

        $response = "🏦 Bank System Özellikleri:\n\n";
        foreach ($features as $title => $description) {
            $response .= "{$title}: {$description}\n";
        }
        
        $response .= "\nDaha detaylı bilgi için 'help' yazabilirsiniz.";
        return $response;
    }

    /**
     * Generate navigation response
     */
    protected function generateNavigationResponse(array $context): string
    {
        $pages = [
            'Dashboard' => 'Anasayfa - Hesap özeti ve hızlı erişim',
            'Accounts' => 'Hesaplar - Banka hesaplarınız',
            'Cards' => 'Kartlar - Kredi kartlarınız', 
            'Transactions' => 'İşlemler - Transfer ve ödemeler',
            'History' => 'Geçmiş - İşlem geçmişiniz',
            'Settings' => 'Ayarlar - Profil ve güvenlik',
            'Support' => 'Destek - Yardım ve iletişim'
        ];

        if (isset($context['mentioned_page'])) {
            $mentioned = strtolower($context['mentioned_page']);
            foreach ($pages as $page => $description) {
                if (strpos(strtolower($page), $mentioned) !== false || 
                    strpos(strtolower($description), $mentioned) !== false) {
                    return "📍 {$page}: {$description}\n\nBu sayfaya menüden erişebilirsiniz.";
                }
            }
        }

        $response = "🗺️ Sayfa Rehberi:\n\n";
        foreach ($pages as $page => $description) {
            $response .= "• {$page}: {$description}\n";
        }
        
        return $response;
    }

    /**
     * Format system information
     */
    protected function formatSystemInfo(array $systemInfo): string
    {
        $response = "ℹ️ Sistem Bilgileri:\n\n";
        
        if (isset($systemInfo['application'])) {
            $app = $systemInfo['application'];
            $response .= "📱 Uygulama: {$app['name']}\n";
            $response .= "🔖 Versiyon: {$app['version']}\n";
            $response .= "🌍 Ortam: {$app['environment']}\n\n";
        }

        if (isset($systemInfo['features'])) {
            $response .= "✨ Özellikler: " . implode(', ', $systemInfo['features']) . "\n\n";
        }

        $response .= "Daha detaylı bilgi için 'features' yazabilirsiniz.";
        return $response;
    }

    /**
     * Generate permissions response
     */
    protected function generatePermissionsResponse($knowledgeService): string
    {
        try {
            $userCapabilities = $knowledgeService->getUserCapabilities();
            
            $response = "🔐 Yetki ve İzinler:\n\n";
            
            if (isset($userCapabilities['permissions'])) {
                $response .= "✅ İzinleriniz:\n";
                foreach ($userCapabilities['permissions'] as $permission) {
                    $response .= "• {$permission}\n";
                }
            }
            
            if (isset($userCapabilities['roles'])) {
                $response .= "\n👤 Rolünüz: " . implode(', ', $userCapabilities['roles']) . "\n";
            }
            
            return $response;
        } catch (\Exception $e) {
            return "🔐 Yetkilendirme bilgilerinizi şu anda görüntüleyemiyorum. Daha sonra tekrar deneyin.";
        }
    }

    /**
     * Generate statistics response
     */
    protected function generateStatsResponse($knowledgeService): string
    {
        try {
            $stats = $knowledgeService->getSystemStatistics();
            
            $response = "📊 Sistem İstatistikleri:\n\n";
            
            foreach ($stats as $key => $value) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $response .= "• {$label}: {$value}\n";
            }
            
            return $response;
        } catch (\Exception $e) {
            return "📊 İstatistik bilgilerine şu anda erişilemiyor. Daha sonra tekrar deneyin.";
        }
    }
}


