<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\BankTransaction;

class OpenAIService
{
    protected string $apiKey;
    protected string $model = 'gpt-4.1-nano';
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
    }

    /**
     * Check if OpenAI API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your-openai-api-key-here';
    }

    /**
     * Generate response using ChatGPT
     */
    public function generateResponse(string $userMessage, array $context = []): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($context);
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage]
            ];

            // Add conversation history if available
            if (!empty($context['conversation_history'])) {
                $historyMessages = [];
                foreach (array_slice($context['conversation_history'], -4) as $msg) {
                    $historyMessages[] = ['role' => 'user', 'content' => $msg['user_message']];
                    $historyMessages[] = ['role' => 'assistant', 'content' => $msg['bot_response']];
                }
                array_splice($messages, 1, 0, $historyMessages);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'response' => $data['choices'][0]['message']['content'] ?? null,
                    'usage' => $data['usage'] ?? null,
                    'model' => $data['model'] ?? $this->model,
                ];
            }

            Log::error('OpenAI API error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build system prompt with user context
     */
    protected function buildSystemPrompt(array $context = []): string
    {
        $basePrompt = "Sen bir banka uygulaması içinde çalışan dijital asistansın.
Adın “Bank Assistant”.

Kullanıcılara TÜRKÇE olarak, banka uygulamasındaki işlemleri
nasıl yapabileceklerini adım adım ve sade bir şekilde anlatırsın.

ANA AMACIN:
Kullanıcı bir işlem yapmak istediğini söylediğinde
(örneğin: “para transferi yapmak istiyorum”),
onu uygulama içindeki doğru menüye yönlendirmek ve
izlemesi gereken adımları net şekilde açıklamaktır.

DAVRANIŞ KURALLARI:
- Kısa, net ve yardımcı cevaplar ver.
- Gereksiz teknik terimler kullanma.
- Maddeler halinde veya numaralı adımlarla anlat.
- Emoji kullanabilirsin ama asla abartma (maks. 1–2 emoji).
- Kullanıcıyı yönlendir, işlem onun yerine yapma.
- “Şuraya gir, bunu seç, bunu onayla” şeklinde anlat.

GÜVENLİK:
- Hassas bilgileri ASLA tam haliyle gösterme.
- Hesap numarası, IBAN, kart numarası gibi bilgileri
  yalnızca son 2–4 hanesi görünecek şekilde maskele.
- Şifre, CVV, OTP gibi bilgileri ASLA isteme veya üretme.

KİŞİSELLEŞTİRME:
- Kullanıcı bilgileri verilmişse (isim, hesap türü, bakiye vb.)
  yanıtlarında bunları doğal ve güvenli şekilde kullan.
- Örnek: “Mevcut vadesiz hesabınızdan işlem yapabilirsiniz.”

YANIT ŞEKLİ:
- Önce kullanıcıyı anladığını belirt.
- Ardından uygulama içi yönlendirmeyi yap.
- Gerekirse ek bir soru sor (örn: “Hangi hesaptan transfer yapmak istiyorsunuz?”)

ÖRNEK YANIT STİLİ:
“Para transferi yapmak istiyorsanız:
1. Ana ekranda **Hesaplarım** bölümüne girin
2. Transfer yapmak istediğiniz hesabı seçin
3. **Para Transferi** → **Havale / EFT** adımına dokunun
4. Alıcı bilgilerini girip işlemi onaylayın ✅”

ASLA:
- Hukuki veya finansal tavsiye verme
- Kullanıcı adına işlem yaptığını söyleme
- Belirsiz veya uydurma bilgi üretme";

        // Add user-specific context if authenticated
        if (Auth::check()) {
            $userContext = $this->getUserBankingContext();
            if (!empty($userContext)) {
                $basePrompt .= "\n\n--- KULLANICI BİLGİLERİ (Bu bilgileri yanıtlarında kullan) ---\n";
                $basePrompt .= $userContext;
            }
        }

        // Add any additional context
        if (!empty($context['additional_info'])) {
            $basePrompt .= "\n\n--- EK BİLGİLER ---\n" . $context['additional_info'];
        }

        return $basePrompt;
    }

    /**
     * Get user's banking context for personalized responses
     */
    protected function getUserBankingContext(): string
    {
        if (!Auth::check()) {
            return "Kullanıcı giriş yapmamış.";
        }

        $user = Auth::user();
        $context = "Kullanıcı Adı: {$user->first_name} {$user->last_name}\n";

        // Get accounts
        $accounts = BankAccount::where('user_id', $user->id)->with(['currency', 'bank'])->get();

        if ($accounts->isNotEmpty()) {
            $context .= "\n💰 HESAPLAR:\n";
            $totalBalance = 0;

            foreach ($accounts as $account) {
                $bankName = $account->bank ? $account->bank->name : 'Bilinmeyen';
                $currencyCode = $account->getCurrencyCode();
                $availableBalance = $account->available_balance ?? 0;
                $maskedNumber = '***' . substr($account->number ?? '', -4);
                $totalBalance += $availableBalance;

                $context .= "- {$account->name} ({$maskedNumber}): " . number_format($availableBalance, 2) . " {$currencyCode} - Banka: {$bankName}\n";
            }

            $context .= "Toplam Bakiye: " . number_format($totalBalance, 2) . " TRY\n";
            $context .= "Toplam Hesap Sayısı: " . $accounts->count() . "\n";
        } else {
            $context .= "\nKullanıcının henüz hesabı yok.\n";
        }

        // Get cards
        $cards = Card::where('user_id', $user->id)->with(['cardType', 'currency'])->get();

        if ($cards->isNotEmpty()) {
            $context .= "\n💳 KARTLAR:\n";

            foreach ($cards as $card) {
                $cardType = $card->cardType ? $card->cardType->name : 'Bilinmeyen';
                $maskedNumber = '**** **** **** ' . substr($card->number, -4);
                $expiryDate = $card->month && $card->year ? sprintf('%02d/%s', $card->month, $card->year) : 'Belirsiz';
                $status = $card->is_active ? 'Aktif' : 'Blokeli';
                $balance = number_format($card->available_balance ?? 0, 2);
                $currencyCode = $card->getCurrencyCode();

                $context .= "- {$cardType} ({$maskedNumber}): {$balance} {$currencyCode} - Son Kullanma: {$expiryDate} - Durum: {$status}\n";
            }

            $context .= "Toplam Kart Sayısı: " . $cards->count() . "\n";
        } else {
            $context .= "\nKullanıcının henüz kartı yok.\n";
        }

        // Get recent transactions
        $recentTransactions = BankTransaction::where('user_id', $user->id)
            ->with(['bankAccount'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($recentTransactions->isNotEmpty()) {
            $context .= "\n📊 SON 5 İŞLEM:\n";

            foreach ($recentTransactions as $transaction) {
                $type = $transaction->type === 'credit' ? '+' : '-';
                $amount = number_format($transaction->amount, 2);
                $date = $transaction->created_at->format('d.m.Y');
                $narration = $transaction->narration ?? 'Açıklama yok';

                $context .= "- {$type}{$amount} TRY - {$narration} ({$date})\n";
            }
        }

        return $context;
    }

    /**
     * Check if message needs banking data
     */
    public function needsBankingData(string $message): bool
    {
        $bankingKeywords = [
            'bakiye', 'hesap', 'kart', 'para', 'transfer', 'işlem', 'ödeme',
            'balance', 'account', 'card', 'money', 'transaction', 'payment',
            'ne kadar', 'kaç para', 'limit', 'borç', 'kredi', 'fatura'
        ];

        $lowerMessage = mb_strtolower($message, 'UTF-8');

        foreach ($bankingKeywords as $keyword) {
            if (str_contains($lowerMessage, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
