<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'picture' => 'https://cdn1.iconfinder.com/data/icons/bokbokstars-121-classic-stock-icons-1/512/person-man.png',
                'first_name' => 'System',
                'middle_name' => '',
                'last_name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'alt_email' => 'admin2@gmail.com',
                'username' => 'admin',
                'phone_number' => '05001234567',
                'email_verified_at' => NULL,
                'password' => bcrypt('#4#4'),
                'country_id' => +90,
                'description' => 'System Administrator - Full Access',
                'address' => 'Turkiye Istanbul, Kadikoy, Merkez',
                'remember_token' => NULL,
                'two_factor_enabled' => true,
                'two_factor_code' => NULL,
                'two_factor_expires_at' => NULL,
                'created_at' => '2025-10-20 15:30:12',
                'updated_at' => '2025-10-20 15:30:12',
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'picture' => 'https://cdn1.iconfinder.com/data/icons/bokbokstars-121-classic-stock-icons-1/512/person-man.png',
                'first_name' => 'Ali',
                'middle_name' => '',
                'last_name' => 'Yilmaz',
                'email' => 'ali@gmail.com',
                'alt_email' => 'ali2@gmail.com',
                'username' => 'ali',
                'phone_number' => '05321234567',
                'email_verified_at' => NULL,
                'password' => bcrypt('#4#4'),
                'country_id' => +90,
                'description' => 'Regular Customer Account',
                'address' => 'Turkiye Ankara, Cankaya, Merkez',
                'remember_token' => NULL,
                'two_factor_enabled' => true,
                'two_factor_code' => NULL,
                'two_factor_expires_at' => NULL,
                'created_at' => '2025-10-22 09:15:45',
                'updated_at' => '2025-10-22 09:15:45',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'picture' => 'https://cdn1.iconfinder.com/data/icons/bokbokstars-121-classic-stock-icons-1/512/person-man.png',
                'first_name' => 'Mehmet',
                'middle_name' => '',
                'last_name' => 'Demir',
                'email' => 'customercare@gmail.com',
                'alt_email' => 'customercare2@gmail.com',
                'username' => 'customercare',
                'phone_number' => '05331234567',
                'email_verified_at' => NULL,
                'password' => bcrypt('#4#4'),
                'country_id' => +90,
                'description' => 'Customer Support Representative',
                'address' => 'Turkiye Izmir, Konak, Merkez',
                'remember_token' => NULL,
                'two_factor_enabled' => true,
                'two_factor_code' => NULL,
                'two_factor_expires_at' => NULL,
                'created_at' => '2025-10-25 14:22:18',
                'updated_at' => '2025-10-25 14:22:18',
                'deleted_at' => NULL,
            ),
        ));


    }
}
