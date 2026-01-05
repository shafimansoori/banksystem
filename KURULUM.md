# Bank System - Kurulum Rehberi

Bu rehber projeyi sıfırdan çalıştırmak için gereken adımları içerir.

## Gereksinimler

Bilgisayarında bunlar yüklü olmalı:
- PHP 8.1 veya üzeri
- Composer
- Node.js ve NPM
- MySQL veya MAMP/XAMPP
- Git

## Kurulum Adımları

### 1. Projeyi İndir

```bash
git clone <repository-url>
cd Bank-System-SM
```

Veya ZIP olarak indirdiysen, klasöre gir:
```bash
cd Bank-System-SM
```

### 2. PHP Bağımlılıklarını Yükle

```bash
composer install
```

Eğer PHP 8.5+ kullanıyorsan ve hata alırsan:
```bash
composer update
```

### 3. Node.js Bağımlılıklarını Yükle

```bash
npm install
```

### 4. Environment Dosyasını Oluştur

```bash
cp .env.example .env
```

### 5. Veritabanını Oluştur

**MySQL ile:**
```bash
mysql -u root -p
```

MySQL konsolunda:
```sql
CREATE DATABASE bank_system;
EXIT;
```

**MAMP kullanıyorsan:**
```bash
mysql -u root -p --port=8889 --host=127.0.0.1
```

Sonra aynı şekilde veritabanını oluştur.

### 6. .env Dosyasını Düzenle

`.env` dosyasını aç ve şu ayarları yap:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bank_system
DB_USERNAME=root
DB_PASSWORD=

# OpenAI API Key (Chatbot ve Fraud Detection için - opsiyonel)
OPENAI_API_KEY=your_openai_api_key_here

# Mail ayarları (2FA için - opsiyonel, olmadan da çalışır)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

**Not:** MAMP kullanıyorsan `DB_PORT=8889` yap.

### 7. Uygulama Anahtarı Oluştur

```bash
php artisan key:generate
```

### 8. Veritabanı Tablolarını ve Örnek Verileri Oluştur

```bash
php artisan migrate:fresh --seed
```

Bu komut:
- Tüm tabloları oluşturur
- Örnek kullanıcılar, hesaplar, kartlar ve işlemler ekler
- Permissions ve roller oluşturur

### 9. Storage Link Oluştur

```bash
php artisan storage:link
```

### 10. Swagger API Dokümantasyonunu Oluştur

```bash
php artisan l5-swagger:generate
```

### 11. Sunucuları Başlat

**İki ayrı terminal aç:**

**Terminal 1 - Laravel Sunucusu:**
```bash
php artisan serve
```

**Terminal 2 - Vite (Frontend):**
```bash
npm run dev
```

### 12. Uygulamaya Eriş

Tarayıcını aç ve git:
```
http://localhost:8000
```

## Giriş Bilgileri

Tüm hesaplarda şifre: `#4#4`

| Rol | Email | Şifre |
|-----|-------|-------|
| Admin | admin@gmail.com | #4#4 |
| Müşteri | ali@gmail.com | #4#4 |
| Customer Care | customercare@gmail.com | #4#4 |

**Not:** 2FA aktif. Mail ayarı yapmadıysan, konsol loglarına bakarak OTP kodunu görebilirsin:
```bash
php artisan serve
```
Login yaptığında terminalde OTP kodu görünecek.

## Swagger API Dokümantasyonu

API dokümantasyonuna erişmek için:
```
http://localhost:8000/api/documentation
```

## Flagged Transactions (Şüpheli İşlemler)

Şüpheli işlemleri görmek için (Admin olarak giriş yap):
```
http://localhost:8000/flagged-transactions
```

## Production için Build

Canlıya çıkacaksan:

```bash
# Frontend build
npm run build

# .env dosyasında
APP_DEBUG=false
APP_ENV=production
```

## Sık Karşılaşılan Sorunlar

### MySQL bağlanamıyor

- MySQL çalışıyor mu kontrol et
- `.env` dosyasındaki port numarası doğru mu kontrol et (MAMP: 8889, normal MySQL: 3306)

### Permission hatası

```bash
chmod -R 775 storage bootstrap/cache
```

### Cache sorunları

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan permission:cache-reset
```

### Swagger yüklenmiyor

```bash
php artisan l5-swagger:generate
php artisan config:clear
php artisan cache:clear
```

## Proje Özellikleri

- ✅ Hesap Yönetimi
- ✅ Banka İşlemleri (Para yatırma/çekme)
- ✅ Kart Yönetimi
- ✅ AI destekli Dolandırıcılık Tespit Sistemi
- ✅ Text Mining ile Şüpheli İşlem Analizi
- ✅ Two-Factor Authentication (2FA)
- ✅ Role-based Access Control
- ✅ AI Chatbot
- ✅ Swagger API Dokümantasyonu
- ✅ Multi-Currency desteği

## Destek

Sorun yaşarsan:
1. Terminal loglarına bak
2. `storage/logs/laravel.log` dosyasına bak
3. Browser console'u kontrol et

---

Başarılar! 🚀
