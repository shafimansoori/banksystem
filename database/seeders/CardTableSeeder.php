<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // note: عند تحديث قاعدة البيانات أولاً يتم حذف الجدول ثم يتم تعبئته بالمعلومات التي بالأسفل
        // note: database yenilediğimizde önce database'daki bilgiler silinir sonra aşağıdaki bilgilerle doldurulur

        \DB::table('cards')->delete();

        \DB::table('cards')->insert(array (
            // Admin kartları (user_id = 1)
            0 =>
            array (
                'id' => 1,
                'user_id' => 1,
                'card_type_id' => 1,
                'currency_id' => 1,
                'available_balance' => 10000.0,
                'ledger_balance' => 20000.0,
                'name' => 'System Administrator',
                'number' => '4685881824504879',
                'month' => '07',
                'year' => '28',
                'cvv' => '506',
                'billing_address' => 'Istanbul, Kadikoy, Turkey',
                'zip_code' => '34000',
                'created_at' => '2025-10-30 12:16:11',
                'updated_at' => '2025-10-30 12:16:11',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'user_id' => 1,
                'card_type_id' => 2,
                'currency_id' => 1,
                'available_balance' => 5000.0,
                'ledger_balance' => 8000.0,
                'name' => 'System Administrator',
                'number' => '5425233430109903',
                'month' => '12',
                'year' => '27',
                'cvv' => '123',
                'billing_address' => 'Istanbul, Kadikoy, Turkey',
                'zip_code' => '34000',
                'created_at' => '2025-10-30 12:16:11',
                'updated_at' => '2025-10-30 12:16:11',
                'deleted_at' => NULL,
            ),
            // Ali kartları (user_id = 2)
            2 =>
            array (
                'id' => 3,
                'user_id' => 2,
                'card_type_id' => 1,
                'currency_id' => 1,
                'available_balance' => 3000.0,
                'ledger_balance' => 5000.0,
                'name' => 'Ali Yilmaz',
                'number' => '4532015112830366',
                'month' => '09',
                'year' => '26',
                'cvv' => '789',
                'billing_address' => 'Ankara, Cankaya, Turkey',
                'zip_code' => '06000',
                'created_at' => '2025-10-30 12:16:11',
                'updated_at' => '2025-10-30 12:16:11',
                'deleted_at' => NULL,
            ),
            // CustomerCare kartları (user_id = 3)
            3 =>
            array (
                'id' => 4,
                'user_id' => 3,
                'card_type_id' => 1,
                'currency_id' => 1,
                'available_balance' => 2500.0,
                'ledger_balance' => 4000.0,
                'name' => 'Mehmet Demir',
                'number' => '4916338506082832',
                'month' => '03',
                'year' => '29',
                'cvv' => '456',
                'billing_address' => 'Izmir, Konak, Turkey',
                'zip_code' => '35000',
                'created_at' => '2025-10-30 12:16:11',
                'updated_at' => '2025-10-30 12:16:11',
                'deleted_at' => NULL,
            ),
        ));
    }
}
