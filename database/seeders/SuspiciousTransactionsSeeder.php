<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankTransaction;
use App\Models\BankAccount;
use Carbon\Carbon;

class SuspiciousTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get random bank accounts
        $accounts = BankAccount::inRandomOrder()->limit(5)->get();

        if ($accounts->isEmpty()) {
            $this->command->warn('No bank accounts found. Please run BankAccountSeeder first.');
            return;
        }

        $suspiciousTransactions = [
            // High Risk - Gambling/Casino
            [
                'narration' => 'Urgent lottery prize claim payment',
                'type' => 'debit',
                'amount' => 15000.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'urgent'. Contains high-risk keyword: 'lottery'. Large transaction amount: $15,000.00. This transaction shows multiple fraud indicators including urgency tactics and lottery-related activity.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Casino winnings withdrawal request',
                'type' => 'credit',
                'amount' => 25000.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'casino'. Large transaction amount: $25,000.00. Gambling-related transaction with unusually high amount.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Online betting platform deposit',
                'type' => 'debit',
                'amount' => 8500.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'betting'. Large transaction amount: $8,500.00. High-risk gambling activity detected.",
                'is_flagged' => true,
            ],

            // Medium Risk - Foreign/Offshore
            [
                'narration' => 'Foreign transfer to offshore account',
                'type' => 'debit',
                'amount' => 12000.00,
                'risk_level' => 'medium',
                'analysis_result' => "Contains medium-risk keyword: 'foreign'. Contains medium-risk keyword: 'offshore'. Large transaction amount: $12,000.00. International transfer to potentially high-risk jurisdiction.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Charity donation transfer',
                'type' => 'debit',
                'amount' => 7500.00,
                'risk_level' => 'medium',
                'analysis_result' => "Contains medium-risk keyword: 'charity'. While charitable giving is legitimate, large donations should be verified for authenticity.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Gift payment to unknown recipient',
                'type' => 'debit',
                'amount' => 5000.00,
                'risk_level' => 'medium',
                'analysis_result' => "Contains medium-risk keyword: 'gift'. Unusual payment pattern detected. Large gift payments may indicate money laundering.",
                'is_flagged' => true,
            ],

            // Low Risk - Large ATM withdrawals
            [
                'narration' => 'ATM cash withdrawal',
                'type' => 'debit',
                'amount' => 3500.00,
                'risk_level' => 'low',
                'analysis_result' => "Contains low-risk keyword: 'atm'. Large ATM withdrawal detected. Monitor for structuring attempts.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Cash withdrawal from branch',
                'type' => 'debit',
                'amount' => 9800.00,
                'risk_level' => 'low',
                'analysis_result' => "Contains low-risk keyword: 'cash'. Large cash withdrawal just below $10,000 threshold. Potential structuring activity.",
                'is_flagged' => true,
            ],

            // High Risk - Prize/Winner scams
            [
                'narration' => 'Prize winner verification fee',
                'type' => 'debit',
                'amount' => 2500.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'prize'. Contains high-risk keyword: 'winner'. Classic advance-fee fraud pattern detected.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Urgent wire transfer for gambling debt',
                'type' => 'debit',
                'amount' => 18000.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'urgent'. Contains high-risk keyword: 'gambling'. Large transaction amount: $18,000.00. Multiple fraud indicators including urgency and gambling.",
                'is_flagged' => true,
            ],

            // Additional suspicious patterns
            [
                'narration' => 'Offshore investment transfer',
                'type' => 'debit',
                'amount' => 22000.00,
                'risk_level' => 'medium',
                'analysis_result' => "Contains medium-risk keyword: 'offshore'. Large transaction amount: $22,000.00. High-risk investment activity.",
                'is_flagged' => true,
            ],
            [
                'narration' => 'Emergency lottery fee payment',
                'type' => 'debit',
                'amount' => 4500.00,
                'risk_level' => 'high',
                'analysis_result' => "Contains high-risk keyword: 'lottery'. Advance-fee scam pattern detected. Fraudulent activity suspected.",
                'is_flagged' => true,
            ],
        ];

        $this->command->info('Creating suspicious transactions...');

        foreach ($suspiciousTransactions as $index => $transData) {
            $account = $accounts[$index % $accounts->count()];

            // Generate transaction code
            $transactionCode = 'TRX' . strtoupper(uniqid());

            BankTransaction::create([
                'user_id' => $account->user_id,
                'bank_account_id' => $account->id,
                'transaction_code' => $transactionCode,
                'narration' => $transData['narration'],
                'type' => $transData['type'],
                'amount' => $transData['amount'],
                'risk_level' => $transData['risk_level'],
                'analysis_result' => $transData['analysis_result'],
                'is_flagged' => $transData['is_flagged'],
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $this->command->info("Created {$transData['risk_level']} risk transaction: {$transData['narration']}");
        }

        // Add some safe transactions for comparison
        $safeTransactions = [
            [
                'narration' => 'Monthly salary deposit',
                'type' => 'credit',
                'amount' => 5000.00,
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
            ],
            [
                'narration' => 'Grocery shopping payment',
                'type' => 'debit',
                'amount' => 150.00,
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
            ],
            [
                'narration' => 'Electric bill payment',
                'type' => 'debit',
                'amount' => 85.50,
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
            ],
            [
                'narration' => 'Online shopping - Electronics',
                'type' => 'debit',
                'amount' => 450.00,
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
            ],
        ];

        $this->command->info('Creating safe transactions for comparison...');

        foreach ($safeTransactions as $transData) {
            $account = $accounts->random();
            $transactionCode = 'TRX' . strtoupper(uniqid());

            BankTransaction::create([
                'user_id' => $account->user_id,
                'bank_account_id' => $account->id,
                'transaction_code' => $transactionCode,
                'narration' => $transData['narration'],
                'type' => $transData['type'],
                'amount' => $transData['amount'],
                'risk_level' => $transData['risk_level'],
                'analysis_result' => $transData['analysis_result'],
                'is_flagged' => $transData['is_flagged'],
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('✓ Successfully created suspicious and safe transactions!');
        $this->command->info('Summary:');
        $this->command->info('- High Risk: ' . BankTransaction::where('risk_level', 'high')->count() . ' transactions');
        $this->command->info('- Medium Risk: ' . BankTransaction::where('risk_level', 'medium')->count() . ' transactions');
        $this->command->info('- Low Risk: ' . BankTransaction::where('risk_level', 'low')->count() . ' transactions');
        $this->command->info('- Safe: ' . BankTransaction::where('risk_level', 'safe')->count() . ' transactions');
        $this->command->info('- Total Flagged: ' . BankTransaction::where('is_flagged', true)->count() . ' transactions');
    }
}
