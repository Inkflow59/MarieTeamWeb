<?php

namespace Database\Factories;

use App\Models\Administrateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdministrateurFactory extends Factory
{
    protected $model = Administrateur::class;

    public function definition()
    {
        return [
            'pseudo' => $this->faker->word(100),
            'emailAdmin' => $this->faker->unique()->safeEmail,
            'mdp' => hash('sha256', $this->faker->password)
        ];
    }
}