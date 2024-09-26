<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trajet;
use Illuminate\Support\Facades\DB;

class TrajetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('trajet')->insert([
            Trajet::factory()->count(10)->create()
        ]);
    }
}