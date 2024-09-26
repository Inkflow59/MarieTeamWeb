<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrateur;
use Illuminate\Support\Facades\DB;

class AdministrateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('administrateur')->insert([
            Administrateur::factory()->count(10)->create()
        ]);
    }
}