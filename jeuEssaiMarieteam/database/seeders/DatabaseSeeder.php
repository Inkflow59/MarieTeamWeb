<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UtilisateurSeeder::class,
            TrajetSeeder::class,
            CapitaineSeeder::class,
            BateauSeeder::class,
            AdministrateurSeeder::class
        ]);
    }
}
