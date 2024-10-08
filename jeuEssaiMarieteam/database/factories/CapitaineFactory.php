<?php

namespace Database\Factories;

use App\Models\Capitaine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CapitaineFactory extends Factory
{
    protected $model = Capitaine::class;
    
    public function definition()
    {
        return [
            'nomCapitaine' => $this->faker->lastName,
            'prenomCapitaine' => $this->faker->name,
            'dateAnnivCapi' => $this->faker->dateTimeBetween('-65 years', '-20 years'),
            'identifiant' => $this->faker->unique()->userName,
            'password' => hash('sha256', $this->faker->password)
        ];
    }
}