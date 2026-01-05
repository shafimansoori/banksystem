<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CardTransaction;
use App\Models\Card;
use Carbon\Carbon;

class CardTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CardTransaction::truncate();

        $cards = Card::all();

        if ($cards->isEmpty()) {
            $this->command->warn('No cards found. Please run CardTableSeeder first.');
            return;
        }

        $transactions = [
            // Normal transactions
            [
                'transaction_code' => 'CARD001',
                'narration' => 'Online shopping - Amazon',
                'amount' => 150.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'transaction_code' => 'CARD002',
                'narration' => 'Restaurant payment',
                'amount' => 75.50,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'transaction_code' => 'CARD003',
                'narration' => 'Gas station payment',
                'amount' => 60.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subDays(3),
            ],
            // Suspicious transactions
            [
                'transaction_code' => 'CARD004',
                'narration' => 'Urgent lottery prize payment',
                'amount' => 5000.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'high',
                'analysis_result' => 'Contains high-risk keyword: \'lottery\'. Contains high-risk keyword: \'urgent\'. Large transaction amount: $5,000.00',
                'is_flagged' => true,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'transaction_code' => 'CARD005',
                'narration' => 'Casino online gambling',
                'amount' => 2500.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'high',
                'analysis_result' => 'Contains high-risk keyword: \'casino\'. Contains high-risk keyword: \'gambling\'.',
                'is_flagged' => true,
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'transaction_code' => 'CARD006',
                'narration' => 'Foreign offshore transfer',
                'amount' => 8000.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'medium',
                'analysis_result' => 'Contains medium-risk keyword: \'foreign\'. Contains medium-risk keyword: \'offshore\'.',
                'is_flagged' => true,
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'transaction_code' => 'CARD007',
                'narration' => 'Grocery store payment',
                'amount' => 120.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'transaction_code' => 'CARD008',
                'narration' => 'Cash withdrawal ATM',
                'amount' => 500.00,
                'type' => 'debit',
                'status' => 'completed',
                'risk_level' => 'low',
                'analysis_result' => 'Contains low-risk keyword: \'cash\'. Contains low-risk keyword: \'atm\'.',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subHours(3),
            ],
        ];

        foreach ($cards as $index => $card) {
            foreach ($transactions as $key => $transaction) {
                CardTransaction::create([
                    'card_id' => $card->id,
                    'user_id' => $card->user_id,
                    'transaction_code' => $transaction['transaction_code'] . '_C' . $card->id,
                    'narration' => $transaction['narration'],
                    'amount' => $transaction['amount'],
                    'type' => $transaction['type'],
                    'status' => $transaction['status'],
                    'risk_level' => $transaction['risk_level'],
                    'analysis_result' => $transaction['analysis_result'],
                    'is_flagged' => $transaction['is_flagged'],
                    'created_at' => $transaction['created_at'],
                    'updated_at' => $transaction['created_at'],
                ]);
            }
        }

        $this->command->info('Card transactions seeded successfully with fraud detection data.');
    }
}
