<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BankTransactionsTableSeeder extends Seeder
{

    /*
     * Auto generated seed file
    Auto generated seed file
    إرسال البيانات بشكل تلقائي إلى قاعدة البيانات
    biligileri otomtik olarak database'e gönderiliyor
     */
    public function run()
    {


        \DB::table('bank_transactions')->delete();

        \DB::table('bank_transactions')->insert(array (
            // Admin transactions (user_id = 1)
            array (
                'id' => 1,
                'transaction_code' => 'TRX001ADMIN',
                'narration' => 'Monthly Salary Deposit',
                'amount' => 5000.0,
                'user_id' => 1,
                'bank_account_id' => 1,
                'type' => 'credit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-23 08:19:21',
                'updated_at' => '2025-10-23 08:19:21',
                'deleted_at' => NULL,
            ),
            array (
                'id' => 2,
                'transaction_code' => 'TRX002ADMIN',
                'narration' => 'Electricity Bill Payment',
                'amount' => 150.0,
                'user_id' => 1,
                'bank_account_id' => 1,
                'type' => 'debit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-23 09:30:00',
                'updated_at' => '2025-10-23 09:30:00',
                'deleted_at' => NULL,
            ),
            array (
                'id' => 3,
                'transaction_code' => 'TRX003ADMIN',
                'narration' => 'Online Shopping - Electronics',
                'amount' => 850.0,
                'user_id' => 1,
                'bank_account_id' => 1,
                'type' => 'debit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-24 14:15:00',
                'updated_at' => '2025-10-24 14:15:00',
                'deleted_at' => NULL,
            ),
            // Ali transactions (user_id = 2)
            array (
                'id' => 4,
                'transaction_code' => 'TRX001ALI',
                'narration' => 'Freelance Payment Received',
                'amount' => 2500.0,
                'user_id' => 2,
                'bank_account_id' => 3,
                'type' => 'credit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-22 10:00:00',
                'updated_at' => '2025-10-22 10:00:00',
                'deleted_at' => NULL,
            ),
            array (
                'id' => 5,
                'transaction_code' => 'TRX002ALI',
                'narration' => 'Rent Payment',
                'amount' => 1200.0,
                'user_id' => 2,
                'bank_account_id' => 3,
                'type' => 'debit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-25 09:00:00',
                'updated_at' => '2025-10-25 09:00:00',
                'deleted_at' => NULL,
            ),
            // CustomerCare transactions (user_id = 3)
            array (
                'id' => 6,
                'transaction_code' => 'TRX001CARE',
                'narration' => 'Salary Deposit',
                'amount' => 3500.0,
                'user_id' => 3,
                'bank_account_id' => 5,
                'type' => 'credit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-20 08:00:00',
                'updated_at' => '2025-10-20 08:00:00',
                'deleted_at' => NULL,
            ),
            array (
                'id' => 7,
                'transaction_code' => 'TRX002CARE',
                'narration' => 'Grocery Shopping',
                'amount' => 280.0,
                'user_id' => 3,
                'bank_account_id' => 5,
                'type' => 'debit',
                'status' => 'successful',
                'risk_level' => 'safe',
                'analysis_result' => 'Transaction appears normal',
                'is_flagged' => false,
                'created_at' => '2025-10-21 16:30:00',
                'updated_at' => '2025-10-21 16:30:00',
                'deleted_at' => NULL,
            ),
        ));


    }
}
