<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountriesTableSeeder::class,
            CurrenciesTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            BanksTableSeeder::class,
            BankLocationsTableSeeder::class,
            CardType::class, // CardType seeder'ı
            UsersTableSeeder::class,
            ModelHasRolesTableSeeder::class,
            BankAccountsTableSeeder::class,
            CardTableSeeder::class,
            BankTransactionsTableSeeder::class,
        ]);
    }
}
