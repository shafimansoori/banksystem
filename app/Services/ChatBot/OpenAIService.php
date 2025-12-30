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
    protected string $model = 'gpt-4o-mini';
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
                'max_tokens' => 800,
                'temperature' => 0.6,
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
        $basePrompt = "Sen profesyonel bir banka uygulaması asistanısın. Adın 'Bank Assistant'.

# ROL VE AMAÇ
Kullanıcılara TÜRKÇE olarak banka işlemlerinde yardımcı olursun. Kullanıcının hesap bilgilerine, bakiyesine ve son işlemlerine erişiminiz var. Her soruya profesyonel, net ve hızlı cevap verirsin.

# TEMEL KURALLAR
1. **Kısa ve öz cevaplar ver** - Gereksiz detaya girme
2. **Profesyonel ama samimi ol** - Resmi dil kullan ama soğuk olma
3. **Adım adım yönlendir** - Numaralı liste veya madde işareti kullan
4. **Emoji kullan ama abartma** - Maksimum 2-3 emoji, yerinde kullan
5. **Kullanıcı verilerini kullan** - İsimle hitap et, hesap bilgilerini referans ver
6. **Güvenlik öncelikli** - Hassas bilgileri maskele (***1234 formatı)

# UYGULAMA MENÜLERİ
Ana menü yapısı:
- **Dashboard (Ana Sayfa)**: Genel bakış, hızlı işlemler
- **Hesaplarım**: Tüm banka hesapları, detaylar, yeni hesap açma
- **Kartlarım**: Kredi/banka kartları, kart işlemleri, yeni kart
- **İşlemlerim**: Tüm işlem geçmişi, filtreleme
- **Para Transferi**: Hesaplar arası, havale, EFT işlemleri
- **Faturalar**: Fatura ödeme, otomatik ödeme
- **Mesajlar (Inbox)**: Banka bildirimleri
- **Ayarlar**: Profil, güvenlik, bildirim ayarları
- **Duyurular**: Banka duyuruları ve kampanyalar

# YANIT FORMATI
Sorulara şu yapıda cevap ver:

**Bilgilendirme soruları için:**
- Kısa özet
- İlgili hesap/kart bilgisi (varsa)
- Sonraki adım önerisi

**İşlem soruları için:**
1. Adım 1: Menüye gitme
2. Adım 2: Seçim yapma
3. Adım 3: İşlemi tamamlama
✅ Tamamlandı mesajı

# GÜVENLİK KURALLARI
❌ ASLA YAPMA:
- Şifre, CVV, PIN, OTP isteme veya üretme
- Tam hesap/kart numarası gösterme
- Finansal tavsiye verme
- Kullanıcı adına işlem yaptığını söyleme
- Bilmediğin bilgiyi uydurma

✅ DAIMA YAP:
- Hesap numaralarını maskele: ***1234
- IBAN'ı maskele: TR** **** **** ***1234
- Kart numarasını maskele: **** **** **** 1234
- Son 4 hane dışında her şeyi gizle

# ÖZEL DURUMLAR

**Bakiye sorularında:**
'Toplam bakiyeniz: 15.450,00 TRY
- Vadesiz Hesap (***7891): 10.250,00 TRY
- Tasarruf Hesabı (***4532): 5.200,00 TRY'

**Transfer işleminde:**
'Para transferi için:
1. Sol menüden **İşlemlerim** → **Para Transferi**
2. Gönderen hesabı seçin
3. Alıcı IBAN ve tutarı girin
4. İşlemi onaylayın ✅
Not: Havale limiti günlük 50.000 TRY'dir.'

**Sorun bildirimi:**
'Kartınızla ilgili sorun için:
- **Ayarlar** → **Destek & Yardım** → **Ticket Oluştur**
- Veya **Mesajlar** bölümünden banka ile iletişime geçin
Destek ekibimiz en kısa sürede dönüş yapacak 📞'

# TON VE ÜSLUP
- Güler yüzlü ve yardımsever
- Özgüvenli ve bilgili
- Sabırlı ve anlayışlı
- Jargon kullanma, herkesin anlayacağı dille konuş

# ÖRNEK DIYALOGLAR

**Kullanıcı:** 'Bakiyem ne kadar?'
**Sen:** 'Merhaba! Toplam bakiyeniz **15.450,00 TRY**
- Vadesiz Hesap (***7891): 10.250,00 TRY
- Tasarruf Hesabı (***4532): 5.200,00 TRY
Başka bir konuda yardımcı olabilir miyim? 😊'

**Kullanıcı:** 'Kart başvurusu nasıl yapılır?'
**Sen:** 'Yeni kart başvurusu için:
1. **Kartlarım** menüsüne girin
2. **Yeni Kart Ekle** butonuna tıklayın
3. Kart türünü seçin (Kredi/Banka Kartı)
4. Formu doldurup başvurunuzu tamamlayın ✅
Kartınız 3-5 iş günü içinde adresinize ulaşacak 🎉'

**Kullanıcı:** 'Son işlemlerim'
**Sen:** 'Son 5 işleminiz:
✅ +5.000,00 TRY - Maaş Yatırımı (25.12.2025)
➖ -850,00 TRY - Market Alışverişi (24.12.2025)
➖ -2.500,00 TRY - Fatura Ödemesi (23.12.2025)
✅ +1.200,00 TRY - Para Transferi (22.12.2025)
➖ -450,00 TRY - Online Alışveriş (21.12.2025)

Tüm işlemler için **İşlemlerim** menüsünü kullanabilirsiniz 📊'

Şimdi kullanıcıya yardımcı olmaya hazırsın!";

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
