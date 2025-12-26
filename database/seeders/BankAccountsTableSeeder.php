<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BankAccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        // note: عند تحديث قاعدة البيانات أولاً يتم حذف الجدول ثم يتم تعبئته بالمعلومات التي بالأسفل
        // note: database yenilediğimizde önce database'daki bilgiler silinir sonra aşağıdaki bilgilerle doldurulur

        \DB::table('bank_accounts')->delete();

        \DB::table('bank_accounts')->insert(array (
            // Admin hesapları (user_id = 1)
            0 =>
            array (
                'id' => 1,
                'name' => 'Admin Primary Account',
                'number' => 'TRO00112231',
                'available_balance' => 50000.0,
                'ledger_balance' => 100000.0,
                'user_id' => 1,
                'bank_id' => 1,
                'bank_location_id' => 1,
                'created_at' => '2025-10-23 08:19:21',
                'updated_at' => '2025-10-24 10:10:29',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Admin Savings Account',
                'number' => 'TRO00112232',
                'available_balance' => 25000.0,
                'ledger_balance' => 50000.0,
                'user_id' => 1,
                'bank_id' => 2,
                'bank_location_id' => 2,
                'created_at' => '2025-10-05 08:19:21',
                'updated_at' => '2025-10-05 22:21:22',
                'deleted_at' => NULL,
            ),
            // Ali hesapları (user_id = 2)
            2 =>
            array (
                'id' => 3,
                'name' => 'Ali Primary Account',
                'number' => 'TRO00112233',
                'available_balance' => 15000.0,
                'ledger_balance' => 20000.0,
                'user_id' => 2,
                'bank_id' => 1,
                'bank_location_id' => 1,
                'created_at' => '2025-10-05 20:59:26',
                'updated_at' => '2025-10-05 22:21:23',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Ali Savings Account',
                'number' => 'TRO00112234',
                'available_balance' => 8000.0,
                'ledger_balance' => 10000.0,
                'user_id' => 2,
                'bank_id' => 2,
                'bank_location_id' => 2,
                'created_at' => '2025-10-10 14:30:00',
                'updated_at' => '2025-10-10 14:30:00',
                'deleted_at' => NULL,
            ),
            // CustomerCare hesapları (user_id = 3)
            4 =>
            array (
                'id' => 5,
                'name' => 'Mehmet Primary Account',
                'number' => 'TRO00112235',
                'available_balance' => 12000.0,
                'ledger_balance' => 18000.0,
                'user_id' => 3,
                'bank_id' => 1,
                'bank_location_id' => 1,
                'created_at' => '2025-10-15 09:00:00',
                'updated_at' => '2025-10-15 09:00:00',
                'deleted_at' => NULL,
            ),
        ));


    }
}
