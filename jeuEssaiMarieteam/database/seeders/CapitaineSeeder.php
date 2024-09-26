<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capitaine;
use Illuminate\Support\Facades\DB;

class CapitaineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('capitaine')->insert([
            Capitaine::factory()->count(10)->create()
        ]);
    }
}