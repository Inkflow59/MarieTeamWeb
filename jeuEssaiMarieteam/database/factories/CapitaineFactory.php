<?php

namespace Database\Factories;

use App\Models\Capitaine;
use Illuminate\Database\Eloquent\Factories\Factory;

class CapitaineFactory extends Factory
{
    protected $model = Capitaine::class;
    
    public function definition()
    {
        return [
            'nomCapitaine' => $this->faker->lastName,
            'prenomCapitaine' => $this->faker->name,
            'dateAnnivCapi' => $this->faker->date,
        ];
    }
}