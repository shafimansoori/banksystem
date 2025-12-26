<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
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

        \DB::table('model_has_roles')->delete();

        \DB::table('model_has_roles')->insert(array (
            0 =>
            array (
                'role_id' => 2, // System-Admin role for admin@gmail.com
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
            ),
            1 =>
            array (
                'role_id' => 1, // Customer role for ali@gmail.com
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            2 =>
            array (
                'role_id' => 3, // customer-care role for shafiullah1@gmail.com
                'model_type' => 'App\\Models\\User',
                'model_id' => 3,
            ),
        ));


    }
}
