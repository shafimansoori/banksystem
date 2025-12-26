# Bank Management System

A comprehensive banking system built with Laravel 10 that provides complete banking operations including account management, transactions, card management, fraud detection with text mining, and an integrated AI-powered chatbot for customer support.

## Features

- **User Management**: Role-based access control with admin, customer, and customer care roles
- **Bank Account Management**: Create and manage multiple bank accounts with different currencies
- **Card Management**: Issue and manage debit/credit cards with transaction tracking
- **Transaction Processing**: Handle deposits, withdrawals, and transfers between accounts
- **Fraud Detection & Text Mining**: AI-powered transaction analysis to detect suspicious activities
- **Two-Factor Authentication (2FA)**: Email-based OTP verification for secure login
- **Multi-Currency Support**: Support for multiple currencies with real-time conversion
- **AI Chatbot**: Intelligent customer support chatbot powered by OpenAI
- **Swagger API Documentation**: Interactive API documentation and testing
- **Bank Locations**: Manage multiple bank branches and locations
- **Announcements System**: System-wide announcements for users
- **Responsive Design**: Modern UI built with Bootstrap 5 and Vite

## Tech Stack

- **Framework**: Laravel 10.48
- **PHP**: 8.1 or higher
- **Database**: MySQL
- **Frontend**: Vite, Bootstrap 5, Sass
- **Authentication**: Laravel Sanctum + 2FA
- **Permissions**: Spatie Laravel Permission
- **API Documentation**: L5-Swagger (OpenAPI 3.0)
- **AI Integration**: OpenAI GPT API
- **Build Tool**: Vite 4.0

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL 5.7+ or MariaDB
- Apache/Nginx web server (or use Laravel's built-in server)
- OpenAI API Key (for chatbot and fraud detection)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Bank-System-SM
```

### 2. Install PHP Dependencies

```bash
composer install
```

If you encounter PHP version compatibility issues with PHP 8.5+, run:
```bash
composer update
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file and configure:

```bash
cp .env.example .env
```

Configure the following in `.env`:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bank_system
DB_USERNAME=root
DB_PASSWORD=

# OpenAI API (for Chatbot & Fraud Detection)
OPENAI_API_KEY=your_openai_api_key_here

# Mail Configuration (for 2FA)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Create Database

Create a MySQL database named `bank_system`:

```bash
mysql -u root -p
```

In MySQL console:
```sql
CREATE DATABASE bank_system;
EXIT;
```

**Note for MAMP users**: If using MAMP, MySQL typically runs on port 8889:
```bash
mysql -u root -p --port=8889 --host=127.0.0.1
```

### 6. Run Migrations and Seeders

This will create all necessary tables and populate them with sample data:

```bash
php artisan migrate:fresh --seed
```

### 7. Create Storage Link

For file uploads and public storage access:

```bash
php artisan storage:link
```

### 8. Generate Swagger Documentation

```bash
php artisan l5-swagger:generate
```

### 9. Start Development Servers

Open two terminal windows:

**Terminal 1** - Laravel Development Server:
```bash
php artisan serve
```

**Terminal 2** - Vite Development Server (for assets):
```bash
npm run dev
```

### 10. Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

## Default User Accounts

The system comes with pre-configured user accounts for testing. All accounts have **Two-Factor Authentication (2FA)** enabled.

| Role | Name | Email | Password |
|------|------|-------|----------|
| **Admin** | System Administrator | admin@gmail.com | #4#4 |
| **Customer** | Ali Yilmaz | ali@gmail.com | #4#4 |
| **Customer Care** | Mehmet Demir | customercare@gmail.com | #4#4 |

### Login Process with 2FA

1. Go to login page and enter email/password
2. A 6-digit OTP code will be sent to your email
3. Enter the OTP code to complete login
4. OTP expires after 10 minutes

## Swagger API Documentation

The system includes comprehensive API documentation using Swagger/OpenAPI 3.0.

### Accessing Swagger UI

After starting the server, navigate to:
```
http://localhost:8000/api/documentation
```

### Features

- **Interactive Testing**: Test API endpoints directly from the browser
- **Authentication**: Use the "Authorize" button to add Bearer token
- **Request/Response Examples**: View sample requests and responses
- **Schema Definitions**: See all data models and their properties

### Regenerating Documentation

If you modify API annotations, regenerate the docs:
```bash
php artisan l5-swagger:generate
```

### API Annotations Location

API documentation is defined using annotations in:
- `app/Http/Controllers/Api/` - API controllers
- `app/Models/` - Model schemas

## Fraud Detection System

The system includes an AI-powered fraud detection module that analyzes transactions in real-time.

### How It Works

1. **Text Mining**: Analyzes transaction narrations for suspicious keywords
2. **Pattern Detection**: Identifies unusual transaction patterns
3. **Risk Scoring**: Assigns risk levels (safe, low, medium, high)
4. **Flagging**: Automatically flags suspicious transactions for review

### Risk Indicators

- **High Risk**: Gambling, lottery, casino, urgent transfers
- **Medium Risk**: Offshore accounts, large foreign transfers, unusual gifts
- **Low Risk**: Large ATM withdrawals, cash transactions near thresholds

### Viewing Flagged Transactions

Admin users can view flagged transactions at:
```
http://localhost:8000/flagged-transactions
```

## Database Structure

The application includes the following main tables:

### Core Tables
- `users` - User accounts and authentication
- `countries` - Country information
- `currencies` - Supported currencies

### Banking Tables
- `banks` - Bank information
- `bank_locations` - Physical bank branches
- `bank_accounts` - Customer bank accounts
- `bank_transactions` - All banking transactions (with fraud analysis fields)

### Card Tables
- `card_types` - Types of cards (Visa, MasterCard, etc.)
- `cards` - Issued cards
- `card_transactions` - Card-specific transactions

### System Tables
- `chat_conversations` - AI chatbot conversations
- `messages` - System messaging
- `announcements` - System announcements
- `permissions` & `roles` - Access control (44 permissions, 3 roles)

## Sample Data

After running seeders, the system includes:

- **3 Users** (Admin, Customer, Customer Care)
- **5 Bank Accounts** across all users
- **4 Cards** (Visa, MasterCard)
- **23 Transactions** including:
  - 7 Normal transactions
  - 12 Flagged suspicious transactions
  - 4 Safe comparison transactions

### Transaction Risk Distribution
- High Risk: 6 transactions
- Medium Risk: 4 transactions
- Low Risk: 2 transactions
- Safe: 11 transactions

## Production Build

To build assets for production:

```bash
npm run build
```

## Troubleshooting

### MySQL Connection Issues

If you see "Can't connect to local MySQL server":
- Ensure MySQL service is running: `brew services start mysql` (macOS with Homebrew)
- For MAMP users: Start servers from MAMP application
- Check MySQL port in `.env` matches your MySQL configuration

### 2FA Email Not Sending

- Verify MAIL settings in `.env`
- For Gmail, use App Password (not regular password)
- Check spam folder for OTP emails

### Swagger Documentation Not Loading

Regenerate the documentation:
```bash
php artisan l5-swagger:generate
php artisan config:clear
php artisan cache:clear
```

### Permission Issues After Seeding

Clear permission cache:
```bash
php artisan permission:cache-reset
php artisan cache:clear
```

### Composer Install Errors

If you encounter package compatibility issues:
```bash
composer update
```

### Permission Denied Errors

Ensure storage and cache directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

## Security Notes

- Change default user passwords before deploying to production
- Update `APP_KEY` in `.env` file: `php artisan key:generate`
- Set `APP_DEBUG=false` in production
- Configure proper database credentials
- Enable HTTPS in production environments
- Keep OpenAI API key secure and never commit to version control

## Support

For issues and questions, please open an issue in the repository.
