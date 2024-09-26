<?php

namespace Database\Factories;

use App\Models\Bateau;
use Illuminate\Database\Eloquent\Factories\Factory;

class BateauFactory extends Factory
{
    protected $model = Bateau::class;

    public function definition()
    {
        return [
            'matricule' => strtoupper($this->faker->unique()->word(10)),
            'modele' => $this->faker->randomElement(['Ferry classique', 'Ferry deluxe', 'Ferry premium']),
            'marque' => $this->faker->company,
            'capaciteHumaine' => $this->faker->numberBetween(1000, 10000),
            'capaciteVehicule' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}