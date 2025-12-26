<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
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

        \DB::table('permissions')->delete();

        // Duplicate permission isimleri kaldırıldı ve düzenlendi
        \DB::table('permissions')->insert(array (
            // Messages
            array ('id' => 1, 'name' => 'can-send-message', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 2, 'name' => 'can-reply-message', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),

            // Login
            array ('id' => 3, 'name' => 'login', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),

            // Cards
            array ('id' => 4, 'name' => 'view-all-cards', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 5, 'name' => 'add-card', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 6, 'name' => 'edit-card', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 7, 'name' => 'delete-card', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:24:25', 'updated_at' => '2025-10-15 07:24:25'),
            array ('id' => 8, 'name' => 'restore-card', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:24:25', 'updated_at' => '2025-10-15 07:24:25'),

            // Bank Accounts
            array ('id' => 9, 'name' => 'view-all-accounts', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 10, 'name' => 'add-account', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 11, 'name' => 'edit-account', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 12, 'name' => 'delete-account', 'guard_name' => 'web', 'created_at' => '2025-10-15 06:16:00', 'updated_at' => '2025-10-15 06:16:00'),
            array ('id' => 13, 'name' => 'restore-account', 'guard_name' => 'web', 'created_at' => '2025-10-15 03:22:00', 'updated_at' => '2025-10-15 03:22:00'),

            // Bank Transactions
            array ('id' => 14, 'name' => 'view-bank-transactions', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 15, 'name' => 'add-bank-transactions', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),
            array ('id' => 16, 'name' => 'view-all-transactions', 'guard_name' => 'web', 'created_at' => '2025-10-15 05:23:00', 'updated_at' => '2025-10-15 05:23:00'),

            // Card Transactions
            array ('id' => 17, 'name' => 'view-card-transactions', 'guard_name' => 'web', 'created_at' => '2025-10-15 10:28:18', 'updated_at' => '2025-10-15 10:28:18'),
            array ('id' => 18, 'name' => 'add-card-transaction', 'guard_name' => 'web', 'created_at' => '2025-10-15 10:15:10', 'updated_at' => '2025-10-15 10:15:10'),

            // Users
            array ('id' => 19, 'name' => 'list-users', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:23:18', 'updated_at' => '2025-10-15 07:23:18'),
            array ('id' => 20, 'name' => 'add-user', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:23:18', 'updated_at' => '2025-10-15 07:23:18'),
            array ('id' => 21, 'name' => 'edit-user', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:23:18', 'updated_at' => '2025-10-15 07:23:18'),
            array ('id' => 22, 'name' => 'delete-user', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:23:18', 'updated_at' => '2025-10-15 07:23:18'),
            array ('id' => 23, 'name' => 'restore-user', 'guard_name' => 'web', 'created_at' => '2025-10-15 07:23:18', 'updated_at' => '2025-10-15 07:23:18'),
            array ('id' => 24, 'name' => 'change-password', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),

            // Currencies
            array ('id' => 25, 'name' => 'list-currencies', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 26, 'name' => 'add-currency', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 27, 'name' => 'edit-currency', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 28, 'name' => 'delete-currency', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 29, 'name' => 'restore-currency', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),

            // Card Types
            array ('id' => 30, 'name' => 'list-card-types', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 31, 'name' => 'add-card-type', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 32, 'name' => 'edit-card-type', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 33, 'name' => 'delete-card-type', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 34, 'name' => 'restore-card-type', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),

            // Banks
            array ('id' => 35, 'name' => 'list-banks', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 36, 'name' => 'add-bank', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 37, 'name' => 'edit-bank', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 38, 'name' => 'delete-bank', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 39, 'name' => 'restore-bank', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),

            // Bank Locations
            array ('id' => 40, 'name' => 'list-bank-locations', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 41, 'name' => 'add-bank-location', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 42, 'name' => 'edit-bank-location', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 43, 'name' => 'delete-bank-location', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
            array ('id' => 44, 'name' => 'restore-bank-location', 'guard_name' => 'web', 'created_at' => '2025-10-15 13:22:20', 'updated_at' => '2025-10-15 13:22:20'),
        ));


    }
}
