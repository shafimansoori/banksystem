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
        // ALL ROLES GET ALL PERMISSIONS (1-44)
        // ============================================
        // For demo purposes, all roles have full access

        // Role 1 = Customer - ALL permissions
        for ($i = 1; $i <= 44; $i++) {
            $permissions[] = ['permission_id' => $i, 'role_id' => 1];
        }

        // Role 2 = System-Admin - ALL permissions
        for ($i = 1; $i <= 44; $i++) {
            $permissions[] = ['permission_id' => $i, 'role_id' => 2];
        }

        // Role 3 = Customer-Care - ALL permissions
        for ($i = 1; $i <= 44; $i++) {
            $permissions[] = ['permission_id' => $i, 'role_id' => 3];
        }

        \DB::table('role_has_permissions')->insert($permissions);


    }
}
