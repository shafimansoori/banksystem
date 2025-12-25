<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
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

        \DB::table('role_has_permissions')->delete();

        // Role IDs:
        // 1 = Customer
        // 2 = System-Admin
        // 3 = customer-care

        $permissions = [];

        // ============================================
        // CUSTOMER PERMISSIONS (Role ID: 1)
        // ============================================
        // Customer can manage their own data

        // Auth & Messaging
        $customerPerms = [
            1,  // can-send-message
            2,  // can-reply-message
            3,  // login
            47, // change-password
        ];

        // Cards - Customer can view, add and manage their own cards
        $customerPerms = array_merge($customerPerms, [
            4,  // view-all-cards (actually views only their own due to controller logic)
            16, // add-card
            17, // edit-card
            18, // delete-card
            20, // view-card-transactions
            21, // add-card-transaction
        ]);

        // Bank Accounts - Customer can view and manage their own accounts
        $customerPerms = array_merge($customerPerms, [
            7,  // view-all-accounts (actually views only their own)
            8,  // add-account
            9,  // edit-account
            13, // delete-account
        ]);

        // Transactions - Customer can view and add transactions
        $customerPerms = array_merge($customerPerms, [
            10, // view-bank-transactions
            11, // add-bank-transactions
        ]);

        foreach ($customerPerms as $permId) {
            $permissions[] = ['permission_id' => $permId, 'role_id' => 1];
        }

        // ============================================
        // SYSTEM-ADMIN PERMISSIONS (Role ID: 2)
        // ============================================
        // Admin has ALL permissions (1-47)

        for ($i = 1; $i <= 47; $i++) {
            $permissions[] = ['permission_id' => $i, 'role_id' => 2];
        }

        // ============================================
        // CUSTOMER-CARE PERMISSIONS (Role ID: 3)
        // ============================================
        // Customer care can help customers but with limited admin powers

        $customerCarePerms = [
            // Auth & Messaging
            1,  // can-send-message
            2,  // can-reply-message
            3,  // login
            47, // change-password

            // View customer data (read-only mostly)
            4,  // view-all-cards
            7,  // view-all-accounts
            10, // view-bank-transactions
            20, // view-card-transactions
            12, // view-all-transactions

            // Can add/edit basic customer data
            8,  // add-account
            9,  // edit-account
            16, // add-card
            17, // edit-card
            11, // add-bank-transactions
            21, // add-card-transaction

            // List reference data
            27, // list-currencies
            32, // list-card-types
            37, // list-banks
            42, // list-bank-locations
        ];

        foreach ($customerCarePerms as $permId) {
            $permissions[] = ['permission_id' => $permId, 'role_id' => 3];
        }

        \DB::table('role_has_permissions')->insert($permissions);


    }
}
