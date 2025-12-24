<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\BankTransaction;

class AdvancedNLPService
{
    protected SystemKnowledgeService $knowledgeService;

    public function __construct(SystemKnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Get the knowledge service instance
     */
    public function getKnowledgeService(): SystemKnowledgeService
    {
        return $this->knowledgeService;
    }
    /**
     * Sentiment analizi için basit kelime listesi
     */
    protected array $positiveWords = [
        'iyi', 'güzel', 'mükemmel', 'harika', 'teşekkürler', 'sağol', 'beğendim', 
        'memnun', 'başarılı', 'süper', 'faydalı', 'kolay', 'hızlı'
    ];

    protected array $negativeWords = [
        'kötü', 'berbat', 'sorun', 'problem', 'hata', 'çalışmıyor', 'yavaş', 
        'karışık', 'zor', 'anlayamıyorum', 'üzgün', 'sinir', 'kızgın', 'şikayet'
    ];

    /**
     * Banking entity patterns
     */
    protected array $entityPatterns = [
        'account_number' => '/\b\d{10,16}\b/',
        'amount' => '/\b\d+(?:[.,]\d{1,2})?\s*(?:tl|lira|euro|dolar|₺|€|\$)\b/i',
        'card_number' => '/\b\d{4}[\s-]*\d{4}[\s-]*\d{4}[\s-]*\d{4}\b/',
        'iban' => '/\bTR\d{2}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{2}\b/i',
        'date' => '/\b\d{1,2}[\/\.-]\d{1,2}[\/\.-]\d{2,4}\b/',
    ];

    /**
     * Intent confidence scoring
     */
    protected array $intentWeights = [
        'exact_match' => 1.0,
        'partial_match' => 0.7,
        'synonym_match' => 0.8,
        'context_match' => 0.6,
    ];

    /**
     * Banking synonyms
     */
    protected array $synonyms = [
        'hesap' => ['account', 'bakiye', 'para', 'balance'],
        'kart' => ['card', 'kartım', 'kredi kartı', 'banka kartı'],
        'transfer' => ['havale', 'gönder', 'aktar', 'transfer et'],
        'borç' => ['debt', 'öde', 'ödeme', 'payment', 'kredi'],
    ];

    /**
     * Perform sentiment analysis on text
     */
    public function analyzeSentiment(string $text): array
    {
        $words = $this->tokenize($text);
        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $positiveCount++;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeCount++;
            }
        }

        $totalWords = count($words);
        $sentiment = 'neutral';
        $confidence = 0.5;

        if ($positiveCount > $negativeCount) {
            $sentiment = 'positive';
            $confidence = min(($positiveCount / max($totalWords, 1)) + 0.3, 1.0);
        } elseif ($negativeCount > $positiveCount) {
            $sentiment = 'negative';
            $confidence = min(($negativeCount / max($totalWords, 1)) + 0.3, 1.0);
        }

        return [
            'sentiment' => $sentiment,
            'confidence' => round($confidence, 3),
            'positive_score' => $positiveCount,
            'negative_score' => $negativeCount,
        ];
    }

    /**
     * Extract banking entities from text
     */
    public function extractEntities(string $text): array
    {
        $entities = [];

        foreach ($this->entityPatterns as $type => $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $entities[$type] = array_unique($matches[0]);
            }
        }

        return $entities;
    }

    /**
     * Enhanced intent detection with context
     */
    public function detectAdvancedIntent(string $message, ?array $context = null): array
    {
        $tokens = $this->tokenize($message);
        $sentiment = $this->analyzeSentiment($message);
        $entities = $this->extractEntities($message);
        
        // Banking-specific intent detection
        $bankingIntents = $this->detectBankingIntents($tokens, $entities);
        
        // System knowledge intents
        $knowledgeIntents = $this->detectKnowledgeIntents($tokens);
        
        // Merge intents
        $allIntents = array_merge($bankingIntents, $knowledgeIntents);
        
        // Personal context from user data
        $personalContext = $this->getPersonalContext();
        
        return [
            'tokens' => $tokens,
            'sentiment' => $sentiment,
            'entities' => $entities,
            'banking_intents' => $bankingIntents,
            'knowledge_intents' => $knowledgeIntents,
            'all_intents' => $allIntents,
            'personal_context' => $personalContext,
            'banking_context' => $this->extractBankingContext($tokens),
            'system_context' => $this->extractSystemContext($tokens),
            'confidence' => $this->calculateOverallConfidence($bankingIntents, $sentiment),
        ];
    }

    /**
     * Detect banking-specific intents with enhanced pattern matching
     */
    protected function detectBankingIntents(array $tokens, array $entities): array
    {
        $intents = [];
        $input = strtolower(implode(' ', $tokens));
        
        // Account balance intent - enhanced patterns
        $balancePatterns = [
            'high' => ['bakiyemi göster', 'bakiyem kaç', 'hesap bakiyem', 'param ne kadar', 'ne kadar param var'],
            'medium' => ['bakiye', 'hesap', 'para', 'balance', 'ne kadar', 'mevcut', 'durum'],
            'keywords' => ['bakiye', 'balance', 'hesap', 'account', 'para', 'money']
        ];
        
        if ($this->matchPatterns($input, $tokens, $balancePatterns)) {
            $intents['account_balance'] = $this->calculateConfidence($input, $tokens, $balancePatterns);
        }
        
        // Detailed accounts listing intent
        $detailedAccountPatterns = [
            'high' => ['tüm hesaplarım', 'hesaplarımı listele', 'all accounts', 'detaylı bakiye', 'hesaplarımı göster'],
            'medium' => ['hesaplar', 'accounts', 'liste', 'list', 'tümü', 'all'],
            'keywords' => ['hesaplar', 'accounts', 'liste', 'list']
        ];
        
        if ($this->matchPatterns($input, $tokens, $detailedAccountPatterns)) {
            $intents['detailed_accounts'] = $this->calculateConfidence($input, $tokens, $detailedAccountPatterns);
        }
        
        // Card operations intent - enhanced patterns  
        $cardPatterns = [
            'high' => ['kartlarımı göster', 'kartlarım', 'kart bilgileri', 'my cards', 'kartlarımı listele'],
            'medium' => ['kart', 'card', 'kredi kartı', 'credit card', 'bloke', 'dondur'],
            'keywords' => ['kart', 'card', 'kredi', 'credit']
        ];
        
        if ($this->matchPatterns($input, $tokens, $cardPatterns)) {
            $intents['card_operations'] = $this->calculateConfidence($input, $tokens, $cardPatterns);
        }
        
        // Transaction history intent - enhanced patterns
        $transactionPatterns = [
            'high' => ['işlem geçmişi', 'son işlemler', 'işlemlerim', 'transaction history', 'hareketlerim'],
            'medium' => ['işlem', 'transaction', 'geçmiş', 'history', 'hareket', 'movement'],
            'keywords' => ['işlem', 'transaction', 'geçmiş', 'history']
        ];
        
        if ($this->matchPatterns($input, $tokens, $transactionPatterns)) {
            $intents['transaction_history'] = $this->calculateConfidence($input, $tokens, $transactionPatterns);
        }
        
        // Transfer intent - enhanced patterns
        $transferPatterns = [
            'high' => ['para gönder', 'transfer yap', 'havale yap', 'para transfer et'],
            'medium' => ['havale', 'transfer', 'gönder', 'aktar', 'wire', 'send money'],
            'keywords' => ['havale', 'transfer', 'gönder', 'send']
        ];
        
        if ($this->matchPatterns($input, $tokens, $transferPatterns) || isset($entities['amount'])) {
            $confidence = $this->calculateConfidence($input, $tokens, $transferPatterns);
            if (isset($entities['amount'])) $confidence += 0.2; // Boost if amount detected
            $intents['money_transfer'] = min($confidence, 1.0);
        }
        
        // Bill payment intent - enhanced patterns
        $billPatterns = [
            'high' => ['fatura öde', 'fatura ödeme', 'bill payment', 'fatura yatır'],
            'medium' => ['fatura', 'öde', 'payment', 'bill', 'borç', 'elektrik', 'su'],
            'keywords' => ['fatura', 'bill', 'öde', 'payment']
        ];
        
        if ($this->matchPatterns($input, $tokens, $billPatterns)) {
            $intents['bill_payment'] = $this->calculateConfidence($input, $tokens, $billPatterns);
        }
        
        return $intents;
    }
    
    /**
     * Match patterns against input
     */
    protected function matchPatterns(string $input, array $tokens, array $patterns): bool
    {
        // Check high confidence patterns first
        foreach ($patterns['high'] as $pattern) {
            if (strpos($input, $pattern) !== false) {
                return true;
            }
        }
        
        // Check medium confidence patterns
        $mediumMatches = 0;
        foreach ($patterns['medium'] as $pattern) {
            if (strpos($input, $pattern) !== false) {
                $mediumMatches++;
            }
        }
        
        // Check keyword matches
        $keywordMatches = 0;
        foreach ($patterns['keywords'] as $keyword) {
            if (in_array($keyword, $tokens) || strpos($input, $keyword) !== false) {
                $keywordMatches++;
            }
        }
        
        return $mediumMatches > 0 || $keywordMatches > 0;
    }
    
    /**
     * Calculate confidence score
     */
    protected function calculateConfidence(string $input, array $tokens, array $patterns): float
    {
        $confidence = 0;
        
        // High confidence patterns
        foreach ($patterns['high'] as $pattern) {
            if (strpos($input, $pattern) !== false) {
                return 0.95; // Very high confidence
            }
        }
        
        // Medium confidence patterns
        foreach ($patterns['medium'] as $pattern) {
            if (strpos($input, $pattern) !== false) {
                $confidence += 0.7;
            }
        }
        
        // Keyword matches
        foreach ($patterns['keywords'] as $keyword) {
            if (in_array($keyword, $tokens) || strpos($input, $keyword) !== false) {
                $confidence += 0.5;
            }
        }
        
        return min($confidence, 1.0);
    }

    /**
     * Get personal context for the authenticated user
     */
    protected function getPersonalContext(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $context = [
            'user_name' => $user->first_name,
            'has_accounts' => false,
            'has_cards' => false,
            'account_count' => 0,
            'card_count' => 0,
            'recent_transactions' => 0,
        ];

        // Get user's banking data
        $accounts = BankAccount::where('user_id', $user->id)->get();
        $cards = Card::where('user_id', $user->id)->get();
        $recentTransactions = BankTransaction::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $context['has_accounts'] = $accounts->count() > 0;
        $context['has_cards'] = $cards->count() > 0;
        $context['account_count'] = $accounts->count();
        $context['card_count'] = $cards->count();
        $context['recent_transactions'] = $recentTransactions;

        return $context;
    }

    /**
     * Generate contextual response based on advanced analysis
     */
    public function generateContextualResponse(array $analysis): string
    {
        $sentiment = $analysis['sentiment']['sentiment'];
        $bankingIntents = $analysis['banking_intents'];
        $personalContext = $analysis['personal_context'];
        
        // Handle negative sentiment with empathy
        if ($sentiment === 'negative') {
            $empathyPhrases = [
                "Üzgünüm, bu durumla karşılaştığınız için. ",
                "Bu sorunu çözmek için buradayım. ",
                "Size yardımcı olmak istiyorum. "
            ];
            $empathy = $empathyPhrases[array_rand($empathyPhrases)];
        } else {
            $empathy = "";
        }

        // Generate response based on highest confidence intent
        if (!empty($bankingIntents)) {
            $topIntent = array_keys($bankingIntents, max($bankingIntents))[0];
            return $empathy . $this->generateIntentResponse($topIntent, $personalContext);
        }

        // Fallback personalized response
        $userName = $personalContext['user_name'] ?? 'Değerli müşterimiz';
        return $empathy . "Merhaba {$userName}! Size nasıl yardımcı olabilirim? Hesaplarınız, kartlarınız veya işlemlerinizle ilgili sorularınızı cevaplayabilirim.";
    }

    /**
     * Generate response for specific banking intent
     */
    protected function generateIntentResponse(string $intent, array $context): string
    {
        $userName = $context['user_name'] ?? '';
        
        switch ($intent) {
            case 'account_balance':
                return $this->generateBalanceResponse($context);
                
            case 'detailed_accounts':
                return $this->generateDetailedAccountsResponse($context);
                
            case 'money_transfer':
                return $this->generateTransferResponse($context);
                
            case 'card_operations':
                return $this->generateCardResponse($context);
                
            case 'transaction_history':
                return $this->generateTransactionHistoryResponse($context);
                
            case 'bill_payment':
                return $this->generateBillPaymentResponse($context);
                
            default:
                return "Size bu konuda yardımcı olmaktan memnuniyet duyarım. Lütfen daha spesifik bilgi verir misiniz?";
        }
    }

    /**
     * Generate detailed balance response with real data (only user's own accounts)
     */
    protected function generateBalanceResponse(array $context): string
    {
        if (!Auth::check()) {
            return "Hesap bilgilerinizi görüntülemek için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $accounts = BankAccount::where('user_id', $user->id)->with(['currency', 'bank'])->get();
        
        if ($accounts->isEmpty()) {
            return "Henüz hesabınız bulunmamaktadır. 'Accounts' sayfasından yeni hesap açabilirsiniz.";
        }

        // Calculate total balances
        $totalAvailable = 0;
        $totalLedger = 0;
        $primaryCurrency = 'TRY';
        
        // Find the primary account or most recent one
        $primaryAccount = $accounts->first();
        
        foreach ($accounts as $account) {
            $currencyCode = $account->getCurrencyCode();
            if ($currencyCode === 'TRY' || !$primaryCurrency) {
                $primaryCurrency = $currencyCode;
            }
            $totalAvailable += $account->available_balance ?? 0;
            $totalLedger += $account->ledger_balance ?? 0;
        }

        $response = "💰 **Bakiye Özeti**\n\n";
        $response .= "👤 Sayın {$user->first_name} {$user->last_name}\n";
        $response .= "📊 Toplam Kullanılabilir Bakiye: " . number_format($totalAvailable, 2) . " {$primaryCurrency}\n";
        $response .= "📈 Toplam Hesap Bakiyesi: " . number_format($totalLedger, 2) . " {$primaryCurrency}\n";
        $response .= "🏦 Toplam Hesap Sayısı: " . $accounts->count() . " hesap\n\n";
        
        // Show primary account details only
        if ($primaryAccount) {
            $bankName = $primaryAccount->bank ? $primaryAccount->bank->name : 'Bilinmeyen Banka';
            $accountNumber = '***' . substr($primaryAccount->number ?? '', -4);
            $availableBalance = number_format($primaryAccount->available_balance ?? 0, 2);
            
            $response .= "🎯 **Ana Hesap Detayı:**\n";
            $response .= "🏦 Banka: {$bankName}\n";
            $response .= "💳 Hesap No: {$accountNumber}\n"; 
            $response .= "💰 Mevcut: {$availableBalance} {$primaryCurrency}\n\n";
        }
        
        $response .= "ℹ️ Tüm hesaplarınızın detayları için 'Accounts' sayfasını ziyaret edin.";
        return $response;
    }
    
    /**
     * Generate detailed accounts listing response
     */
    protected function generateDetailedAccountsResponse(array $context): string
    {
        if (!Auth::check()) {
            return "Hesap bilgilerinizi görüntülemek için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $accounts = BankAccount::where('user_id', $user->id)->with(['currency', 'bank'])->get();
        
        if ($accounts->isEmpty()) {
            return "Henüz hesabınız bulunmamaktadır. 'Accounts' sayfasından yeni hesap açabilirsiniz.";
        }

        $response = "🏦 **Tüm Hesaplarınız**\n\n";
        
        foreach ($accounts as $index => $account) {
            $bankName = $account->bank ? $account->bank->name : 'Bilinmeyen Banka';
            $currencyCode = $account->getCurrencyCode();
            $availableBalance = number_format($account->available_balance ?? 0, 2);
            $ledgerBalance = number_format($account->ledger_balance ?? 0, 2);
            $accountNumber = '***' . substr($account->number, -4);
            
            $response .= "📋 **Hesap " . ($index + 1) . ":**\n";
            $response .= "🏦 Banka: {$bankName}\n";
            $response .= "💳 Hesap: {$account->name} ({$accountNumber})\n";
            $response .= "💰 Kullanılabilir: {$availableBalance} {$currencyCode}\n";
            $response .= "📊 Genel Bakiye: {$ledgerBalance} {$currencyCode}\n\n";
        }
        
        $response .= "ℹ️ Detaylı işlemler için 'Accounts' sayfasını ziyaret edin.";
        return $response;
    }

    /**
     * Generate transfer response with account info
     */
    protected function generateTransferResponse(array $context): string
    {
        if (!Auth::check()) {
            return "Transfer işlemleri için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $accounts = BankAccount::where('user_id', $user->id)->get();
        
        if ($accounts->isEmpty()) {
            return "Transfer yapmak için önce bir hesabınızın olması gerekmektedir. 'Accounts' sayfasından hesap açabilirsiniz.";
        }

        $response = "💸 Para transferi yapmak için:\n\n";
        $response .= "1. 'Accounts' sayfasına gidin\n";
        $response .= "2. Transfer yapmak istediğiniz hesabı seçin\n";
        $response .= "3. 'Transfer' düğmesine tıklayın\n\n";
        
        $response .= "Mevcut hesaplarınız:\n";
        foreach ($accounts->take(3) as $account) {
            $currencyCode = $account->currency->code ?? 'TRY';
            $balance = number_format($account->available_balance, 2);
            $response .= "• {$account->name}: {$balance} {$currencyCode}\n";
        }
        
        $response .= "\n⚠️ Transfer için IBAN, tutar ve açıklama bilgilerini hazırlayın.";
        return $response;
    }

    /**
     * Generate card response with enhanced real card data
     */
    protected function generateCardResponse(array $context): string
    {
        if (!Auth::check()) {
            return "Kart bilgilerinizi görüntülemek için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $cards = Card::where('user_id', $user->id)->with(['cardType', 'currency'])->get();
        
        if ($cards->isEmpty()) {
            return "❌ Henüz kartınız bulunmamaktadır.\n\n🛠️ Yeni kart başvurusu için:\n• 'Cards' sayfasına gidin\n• 'Apply for New Card' butonunu kullanın\n• Kart türünüzü seçin";
        }

        $response = "💳 Kart Bilgileriniz:\n\n";
        
        foreach ($cards as $card) {
            $cardType = $card->cardType ? $card->cardType->name : 'Bilinmeyen Tip';
            $maskedNumber = "**** **** **** " . substr($card->number, -4);
            $expiryDate = $card->month && $card->year ? sprintf('%02d/%s', $card->month, $card->year) : 'Belirsiz';
            $isActive = $card->is_active ? '🟢 Aktif' : '🔴 Blokeli';
            
            // Kullanılabilir limit hesaplama
            $availableBalance = $card->available_balance ?? 0;
            $currencyCode = $card->getCurrencyCode();
            $balanceFormatted = number_format($availableBalance, 2) . " {$currencyCode}";
            
            $response .= "📇 Kart: {$maskedNumber}\n";
            $response .= "🏷️ Tip: {$cardType}\n";
            $response .= "📅 Geçerlilik: {$expiryDate}\n";
            $response .= "💰 Kullanılabilir: {$balanceFormatted}\n";
            $response .= "🔄 Durum: {$isActive}\n";
            $response .= "🔐 Güvenlik: CVV korunmaktadır\n\n";
        }
        
        $response .= "🛠️ Kart İşlemleri:\n";
        $response .= "• Kart bloke/aktifleştirme\n";
        $response .= "• Limit artırma\n";
        $response .= "• Yeni PIN talep etme\n";
        $response .= "• Detaylar için 'Cards' sayfasını ziyaret edin";
        
        return $response;
    }

    /**
     * Generate transaction history response
     */
    protected function generateTransactionHistoryResponse(array $context): string
    {
        if (!Auth::check()) {
            return "İşlem geçmişi için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $recentTransactions = BankTransaction::where('user_id', $user->id)
            ->with(['bankAccount'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        if ($recentTransactions->isEmpty()) {
            return "Henüz işlem geçmişiniz bulunmamaktadır.";
        }

        $response = "📊 Son İşlemleriniz:\n\n";
        foreach ($recentTransactions as $transaction) {
            $accountName = $transaction->bankAccount->name ?? 'Bilinmeyen Hesap';
            $amount = number_format($transaction->amount, 2);
            $type = $transaction->type === 'credit' ? '➕' : '➖';
            $date = $transaction->created_at->format('d.m.Y');
            
            $response .= "{$type} {$amount} TRY - {$transaction->narration}\n";
            $response .= "📅 {$date} | 🏦 {$accountName}\n\n";
        }
        
        $response .= "Detaylı işlem geçmişi için 'Transactions' sayfasını ziyaret edebilirsiniz.";
        return $response;
    }

    /**
     * Generate bill payment response
     */
    protected function generateBillPaymentResponse(array $context): string
    {
        if (!Auth::check()) {
            return "Fatura ödeme işlemleri için önce giriş yapmanız gerekmektedir.";
        }

        $user = Auth::user();
        $accounts = BankAccount::where('user_id', $user->id)->get();
        
        if ($accounts->isEmpty()) {
            return "Fatura ödemesi yapmak için önce bir hesabınızın olması gerekmektedir.";
        }

        $response = "🧾 Fatura Ödeme Seçenekleri:\n\n";
        $response .= "1. 'Accounts' sayfasından ödeme yapacağınız hesabı seçin\n";
        $response .= "2. 'Pay Bills' seçeneğini kullanın\n";
        $response .= "3. Fatura türünü seçin (elektrik, su, telefon, vs.)\n\n";
        
        $totalBalance = $accounts->sum('available_balance');
        $response .= "💰 Toplam Kullanılabilir Bakiye: " . number_format($totalBalance, 2) . " TRY\n\n";
        
        $response .= "📱 Fatura ödeme yöntemleri:\n";
        $response .= "• QR kod ile ödeme\n";
        $response .= "• Barkod ile ödeme\n";
        $response .= "• Manuel bilgi girişi\n";
        $response .= "• Otomatik ödeme talimatı";
        
        return $response;
    }

    /**
     * Calculate overall confidence score
     */
    protected function calculateOverallConfidence(array $bankingIntents, array $sentiment): float
    {
        $intentConfidence = !empty($bankingIntents) ? max($bankingIntents) : 0.3;
        $sentimentConfidence = $sentiment['confidence'];
        
        return round(($intentConfidence + $sentimentConfidence) / 2, 3);
    }

    /**
     * Tokenize text (same as base service but with additional processing)
     */
    protected function tokenize(string $text): array
    {
        $text = Str::of($text)
            ->lower()
            ->replaceMatches('/[^a-zA-Z0-9ğüşöçıİĞÜŞÖÇ\s]+/u', ' ')
            ->replaceMatches('/\s+/u', ' ')
            ->trim();

        $tokens = array_filter(explode(' ', (string) $text), static fn ($token) => $token !== '');

        return array_values(array_unique($tokens));
    }

    /**
     * Detect knowledge/system-related intents
     */
    private function detectKnowledgeIntents($tokens)
    {
        $intents = [];
        $input = strtolower(implode(' ', $tokens));
        
        // Help and information requests
        $helpPatterns = [
            'help' => ['help', 'yardım', 'nasıl', 'how', 'ne yapabilirim', 'what can'],
            'features' => ['özellik', 'feature', 'neler yapabilir', 'what features', 'ne işe yarar'],
            'navigation' => ['nereye', 'where', 'sayfa', 'page', 'git', 'go to', 'menu'],
            'system_info' => ['hakkında', 'about', 'sistem', 'system', 'bilgi', 'info', 'versiyon', 'version'],
            'permissions' => ['yetki', 'permission', 'izin', 'access', 'yapabilir miyim', 'can i'],
            'statistics' => ['istatistik', 'statistic', 'rapor', 'report', 'özet', 'summary', 'kaç', 'how many']
        ];
        
        foreach ($helpPatterns as $intent => $patterns) {
            $score = 0;
            foreach ($patterns as $pattern) {
                if (strpos($input, $pattern) !== false) {
                    $score += 0.8;
                }
            }
            
            if ($score > 0) {
                $intents[] = [
                    'intent' => "system_$intent",
                    'confidence' => min($score, 1.0)
                ];
            }
        }
        
        return $intents;
    }

    /**
     * Extract system context from user input
     */
    private function extractSystemContext($tokens)
    {
        $context = [];
        $input = strtolower(implode(' ', $tokens));
        
        // Page/navigation mentions
        $pages = ['dashboard', 'anasayfa', 'hesap', 'account', 'kart', 'card', 'transfer', 'transaction', 'işlem'];
        foreach ($pages as $page) {
            if (strpos($input, $page) !== false) {
                $context['mentioned_page'] = $page;
                break;
            }
        }
        
        // Action mentions
        $actions = ['create', 'oluştur', 'add', 'ekle', 'delete', 'sil', 'edit', 'düzenle', 'view', 'görüntüle'];
        foreach ($actions as $action) {
            if (strpos($input, $action) !== false) {
                $context['mentioned_action'] = $action;
                break;
            }
        }
        
        // Question words
        $questionWords = ['ne', 'what', 'nasıl', 'how', 'neden', 'why', 'nerede', 'where', 'kim', 'who'];
        foreach ($questionWords as $word) {
            if (strpos($input, $word) !== false) {
                $context['question_type'] = $word;
                break;
            }
        }
        
        return $context;
    }

    /**
     * Extract banking context from user input
     */
    private function extractBankingContext($tokens)
    {
        $context = [];
        
        // Transaction patterns
        if (in_array('transfer', $tokens) || in_array('send', $tokens) || in_array('gönder', $tokens)) {
            $context['transaction_type'] = 'transfer';
        }
        
        if (in_array('withdraw', $tokens) || in_array('çek', $tokens)) {
            $context['transaction_type'] = 'withdrawal';
        }
        
        if (in_array('deposit', $tokens) || in_array('yatır', $tokens)) {
            $context['transaction_type'] = 'deposit';
        }
        
        // Account patterns
        if (in_array('account', $tokens) || in_array('hesap', $tokens)) {
            $context['entity_type'] = 'account';
        }
        
        if (in_array('card', $tokens) || in_array('kart', $tokens)) {
            $context['entity_type'] = 'card';
        }
        
        // Amount patterns
        $amountPattern = '/\d+[\.,]?\d*\s*(tl|lira|dollar|\$|₺|usd|eur|euro)/i';
        $input = implode(' ', $tokens);
        if (preg_match($amountPattern, $input, $matches)) {
            $context['has_amount'] = true;
            $context['amount_mention'] = $matches[0];
        }
        
        return $context;
    }
}