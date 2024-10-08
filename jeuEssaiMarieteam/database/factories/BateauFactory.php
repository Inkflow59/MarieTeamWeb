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
            'matricule' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'modele' => $this->faker->randomElement(['Ferry classique', 'Ferry deluxe', 'Ferry premium']),
            'marque' => $this->faker->company,
            'capaciteHumaine' => $this->faker->numberBetween(900, 3000),
            'capaciteVehicules' => $this->faker->numberBetween(100, 300),
        ];
    }
}