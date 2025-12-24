<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Card;
use App\Models\BankTransaction;
use App\Models\Bank;
use App\Models\BankLocation;
use App\Models\Currency;
use App\Models\Message;

class SystemKnowledgeService
{
    /**
     * Get comprehensive system information for AI responses
     */
    public function getSystemKnowledge(): array
    {
        return [
            'application_info' => $this->getApplicationInfo(),
            'user_capabilities' => $this->getUserCapabilities(),
            'banking_features' => $this->getBankingFeatures(),
            'system_statistics' => $this->getSystemStatistics(),
            'available_routes' => $this->getAvailableRoutes(),
            'user_permissions' => $this->getUserPermissions(),
            'system_status' => $this->getSystemStatus(),
        ];
    }

    /**
     * Get basic application information
     */
    protected function getApplicationInfo(): array
    {
        return [
            'name' => config('app.name', 'Bank System'),
            'version' => '1.0.0',
            'description' => 'Comprehensive Banking Management System',
            'features' => [
                'Multi-bank account management',
                'Credit/Debit card operations',
                'Transaction tracking and history',
                'Money transfer capabilities',
                'Bill payment system',
                'Currency exchange support',
                'Real-time balance checking',
                'Secure messaging system',
                'AI-powered customer support',
                'Multi-language support',
            ],
            'supported_languages' => ['Turkish', 'English'],
            'main_modules' => [
                'Dashboard' => 'Ana sayfa ve genel bakış',
                'Accounts' => 'Banka hesabı yönetimi',
                'Cards' => 'Kart işlemleri ve yönetimi',
                'Transactions' => 'İşlem geçmişi ve raporları',
                'Currencies' => 'Döviz kurları ve işlemleri',
                'Messages' => 'Güvenli mesajlaşma sistemi',
                'Profile' => 'Kullanıcı profil yönetimi',
                'Bank Locations' => 'Şube ve ATM lokasyonları',
            ],
        ];
    }

    /**
     * Get user-specific capabilities
     */
    protected function getUserCapabilities(): array
    {
        if (!Auth::check()) {
            return [
                'authenticated' => false,
                'available_actions' => ['login', 'register', 'password_reset'],
            ];
        }

        $user = Auth::user();
        
        return [
            'authenticated' => true,
            'user_type' => $this->getUserType($user),
            'account_management' => [
                'view_balances' => true,
                'create_accounts' => true,
                'close_accounts' => true,
                'transfer_money' => true,
                'view_statements' => true,
            ],
            'card_management' => [
                'view_cards' => true,
                'request_new_card' => true,
                'block_unblock_cards' => true,
                'set_card_limits' => true,
            ],
            'transaction_capabilities' => [
                'send_money' => true,
                'receive_money' => true,
                'pay_bills' => true,
                'schedule_payments' => true,
                'view_history' => true,
            ],
            'communication' => [
                'send_messages' => $this->userCanSendMessages($user),
                'contact_support' => true,
                'use_chatbot' => true,
            ],
        ];
    }

    /**
     * Get available banking features
     */
    protected function getBankingFeatures(): array
    {
        return [
            'account_types' => [
                'checking' => 'Vadesiz hesap - günlük işlemler için',
                'savings' => 'Tasarruf hesabı - faiz kazanmak için',
                'business' => 'İşletme hesabı - ticari işlemler için',
            ],
            'supported_currencies' => $this->getSupportedCurrencies(),
            'available_banks' => $this->getAvailableBanks(),
            'transaction_types' => [
                'EFT' => 'Elektronik Fon Transferi',
                'SWIFT' => 'Uluslararası para transferi',
                'HAVALE' => 'Aynı banka içi transfer',
                'ATM' => 'ATM işlemleri',
                'POS' => 'Kartla ödeme',
                'ONLINE' => 'Online bankacılık',
            ],
            'card_types' => [
                'debit' => 'Banka kartı - hesaptaki para ile',
                'credit' => 'Kredi kartı - önceden tanımlı limit ile',
                'prepaid' => 'Ön ödemeli kart - yüklenmiş bakiye ile',
            ],
            'security_features' => [
                'two_factor_auth' => 'İki faktörlü kimlik doğrulama',
                'encryption' => 'Uçtan uca şifreleme',
                'fraud_detection' => 'Dolandırıcılık tespiti',
                'secure_messaging' => 'Güvenli mesajlaşma',
            ],
        ];
    }

    /**
     * Get system statistics
     */
    protected function getSystemStatistics(): array
    {
        try {
            return [
                'total_users' => User::count(),
                'active_accounts' => BankAccount::count(),
                'total_cards' => Card::count(),
                'recent_transactions' => BankTransaction::where('created_at', '>=', now()->subDays(30))->count(),
                'supported_banks' => Bank::count(),
                'bank_locations' => BankLocation::count(),
                'supported_currencies' => Currency::count(),
                'system_messages' => Message::count(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'İstatistik bilgileri şu anda alınamıyor',
            ];
        }
    }

    /**
     * Get available routes for navigation help
     */
    protected function getAvailableRoutes(): array
    {
        return [
            'dashboard' => [
                'url' => '/dashboard',
                'description' => 'Ana sayfa - genel bakış ve özet bilgiler',
            ],
            'accounts' => [
                'url' => '/accounts',
                'description' => 'Hesap yönetimi - bakiye, transfer, işlemler',
            ],
            'cards' => [
                'url' => '/cards',
                'description' => 'Kart yönetimi - blokaj, limit, yeni kart',
            ],
            'transactions' => [
                'url' => '/transactions',
                'description' => 'İşlem geçmişi - tüm finansal hareketler',
            ],
            'currencies' => [
                'url' => '/currencies',
                'description' => 'Döviz kurları ve para birimi işlemleri',
            ],
            'messages' => [
                'url' => '/messages',
                'description' => 'Mesajlaşma - güvenli iletişim sistemi',
            ],
            'profile' => [
                'url' => '/profile',
                'description' => 'Profil yönetimi - kişisel bilgi güncelleme',
            ],
            'bank-locations' => [
                'url' => '/bank-locations',
                'description' => 'Şube ve ATM konumları',
            ],
        ];
    }

    /**
     * Get user permissions
     */
    protected function getUserPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        
        try {
            $roles = ['Customer']; // Default
            $permissions = [];
            
            // Safely try to get roles and permissions
            if (method_exists($user, 'getRoleNames')) {
                $roles = $user->getRoleNames()->toArray();
            }
            if (method_exists($user, 'getAllPermissions')) {
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            }
            
            return [
                'roles' => $roles,
                'permissions' => $permissions,
            ];
        } catch (\Exception $e) {
            return [
                'roles' => ['Customer'], // Default role
                'permissions' => [],
            ];
        }
    }

    /**
     * Get system status
     */
    protected function getSystemStatus(): array
    {
        return [
            'online' => true,
            'maintenance_mode' => app()->isDownForMaintenance(),
            'database_connected' => $this->isDatabaseConnected(),
            'last_updated' => now()->format('Y-m-d H:i:s'),
            'version' => '1.0.0',
        ];
    }

    /**
     * Get supported currencies
     */
    protected function getSupportedCurrencies(): array
    {
        try {
            return Currency::select('code', 'name')->get()->pluck('name', 'code')->toArray();
        } catch (\Exception $e) {
            return [
                'TRY' => 'Turkish Lira',
                'USD' => 'US Dollar',
                'EUR' => 'Euro',
                'GBP' => 'British Pound',
            ];
        }
    }

    /**
     * Get available banks
     */
    protected function getAvailableBanks(): array
    {
        try {
            return Bank::select('code', 'name')->get()->pluck('name', 'code')->toArray();
        } catch (\Exception $e) {
            return [
                'TRZR' => 'Ziraat Bankası',
                'TRHK' => 'Halkbank',
                'TRVK' => 'Vakıfbank',
            ];
        }
    }

    /**
     * Determine user type
     */
    protected function getUserType($user): string
    {
        try {
            if ($user->hasRole('System-Admin')) {
                return 'System Administrator';
            } elseif ($user->hasRole('customer-care')) {
                return 'Customer Care Representative';
            } else {
                return 'Customer';
            }
        } catch (\Exception $e) {
            return 'Customer';
        }
    }

    /**
     * Check if user can send messages
     */
    protected function userCanSendMessages($user): bool
    {
        try {
            return $user->can('can-send-message');
        } catch (\Exception $e) {
            return true; // Default allow
        }
    }

    /**
     * Check if database is connected
     */
    protected function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate comprehensive help response
     */
    public function generateHelpResponse($context = []): string
    {
        $knowledge = $this->getSystemKnowledge();
        
        // Extract topic from context if array is passed
        $topic = 'general';
        if (is_array($context)) {
            if (isset($context['mentioned_page'])) {
                $topic = $context['mentioned_page'];
            } elseif (isset($context['mentioned_action'])) {
                $topic = $context['mentioned_action'];
            } elseif (isset($context['question_type'])) {
                $topic = $context['question_type'];
            }
        } elseif (is_string($context)) {
            $topic = $context;
        }
        
        switch (strtolower($topic)) {
            case 'navigation':
            case 'sayfalar':
            case 'menü':
            case 'dashboard':
            case 'account':
            case 'card':
                return $this->generateNavigationHelp($knowledge['available_routes']);
                
            case 'features':
            case 'özellikler':
                return $this->generateFeaturesHelp($knowledge['banking_features']);
                
            case 'hesap':
                return $this->generateAccountHelp($knowledge['user_capabilities']);
                
            case 'kart':
                return $this->generateCardHelp($knowledge['user_capabilities']);
                
            case 'security':
            case 'güvenlik':
                return $this->generateSecurityHelp($knowledge['banking_features']);
                
            default:
                return $this->generateGeneralHelp($knowledge);
        }
    }

    /**
     * Generate navigation help
     */
    protected function generateNavigationHelp(array $routes): string
    {
        $help = "🧭 **Navigasyon Rehberi:**\n\n";
        
        foreach ($routes as $name => $info) {
            $help .= "📄 **" . ucfirst($name) . "**: " . $info['description'] . "\n";
        }
        
        $help .= "\n💡 Herhangi bir sayfaya gitmek için sol menüden ilgili bölümü seçebilirsiniz.";
        
        return $help;
    }

    /**
     * Generate features help
     */
    protected function generateFeaturesHelp(array $features): string
    {
        $help = "✨ **Sistem Özellikleri:**\n\n";
        
        $help .= "🏦 **Hesap Türleri:**\n";
        foreach ($features['account_types'] as $type => $desc) {
            $help .= "• " . ucfirst($type) . ": " . $desc . "\n";
        }
        
        $help .= "\n💳 **Kart Türleri:**\n";
        foreach ($features['card_types'] as $type => $desc) {
            $help .= "• " . ucfirst($type) . ": " . $desc . "\n";
        }
        
        $help .= "\n🔒 **Güvenlik:**\n";
        foreach ($features['security_features'] as $feature => $desc) {
            $help .= "• " . $desc . "\n";
        }
        
        return $help;
    }

    /**
     * Generate account help
     */
    protected function generateAccountHelp(array $capabilities): string
    {
        if (!$capabilities['authenticated']) {
            return "Hesap işlemleri için önce giriş yapmanız gerekmektedir.";
        }
        
        $help = "💰 **Hesap İşlemleri:**\n\n";
        
        if ($capabilities['account_management']['view_balances']) {
            $help .= "• Bakiye sorgulama\n";
        }
        if ($capabilities['account_management']['transfer_money']) {
            $help .= "• Para transferi\n";
        }
        if ($capabilities['account_management']['create_accounts']) {
            $help .= "• Yeni hesap açma\n";
        }
        
        $help .= "\n🔧 **Nasıl Kullanılır:**\n";
        $help .= "1. 'Accounts' sayfasına gidin\n";
        $help .= "2. İlgili hesabı seçin\n";
        $help .= "3. Yapmak istediğiniz işlemi seçin\n";
        
        return $help;
    }

    /**
     * Generate card help
     */
    protected function generateCardHelp(array $capabilities): string
    {
        if (!$capabilities['authenticated']) {
            return "Kart işlemleri için önce giriş yapmanız gerekmektedir.";
        }
        
        $help = "💳 **Kart İşlemleri:**\n\n";
        
        if ($capabilities['card_management']['view_cards']) {
            $help .= "• Kart bilgilerini görüntüleme\n";
        }
        if ($capabilities['card_management']['block_unblock_cards']) {
            $help .= "• Kart blokaj/blokaj kaldırma\n";
        }
        if ($capabilities['card_management']['request_new_card']) {
            $help .= "• Yeni kart başvurusu\n";
        }
        
        $help .= "\n⚠️ **Acil Durumlar:**\n";
        $help .= "• Kartınız çalındı → Hemen bloke edin\n";
        $help .= "• Şüpheli işlem → Destek ekibiyle iletişime geçin\n";
        
        return $help;
    }

    /**
     * Generate security help
     */
    protected function generateSecurityHelp(array $features): string
    {
        $help = "🔐 **Güvenlik Bilgileri:**\n\n";
        
        $help .= "✅ **Güvenlik Önlemleri:**\n";
        foreach ($features['security_features'] as $feature => $desc) {
            $help .= "• " . $desc . "\n";
        }
        
        $help .= "\n⚠️ **Dikkat Edilmesi Gerekenler:**\n";
        $help .= "• Şifrelerinizi kimseyle paylaşmayın\n";
        $help .= "• Şüpheli e-postalar/SMS'lere dikkat edin\n";
        $help .= "• Düzenli olarak hesap hareketlerinizi kontrol edin\n";
        $help .= "• Güvenli internet bağlantısı kullanın\n";
        
        return $help;
    }

    /**
     * Generate general help
     */
    protected function generateGeneralHelp(array $knowledge): string
    {
        $app = $knowledge['application_info'];
        $stats = $knowledge['system_statistics'];
        
        $help = "🏦 **" . $app['name'] . " Yardım Sistemi**\n\n";
        $help .= "📝 **Açıklama:** " . $app['description'] . "\n\n";
        
        $help .= "🌟 **Ana Özellikler:**\n";
        foreach ($app['features'] as $feature) {
            $help .= "• " . $feature . "\n";
        }
        
        $help .= "\n📊 **Sistem Durumu:**\n";
        if (isset($stats['total_users'])) {
            $help .= "• Toplam Kullanıcı: " . $stats['total_users'] . "\n";
            $help .= "• Aktif Hesaplar: " . $stats['active_accounts'] . "\n";
            $help .= "• Desteklenen Bankalar: " . $stats['supported_banks'] . "\n";
        }
        
        $help .= "\n💬 **Yardım Komutları:**\n";
        $help .= "• 'navigasyon yardım' - Sayfa rehberi\n";
        $help .= "• 'hesap yardım' - Hesap işlemleri\n";
        $help .= "• 'kart yardım' - Kart işlemleri\n";
        $help .= "• 'güvenlik yardım' - Güvenlik bilgileri\n";
        
        return $help;
    }
}