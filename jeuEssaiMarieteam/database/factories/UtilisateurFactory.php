<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => hash('sha256', $this->faker->password),
            'nomUtilisateur' => $this->faker->lastName,
            'prenomUtilisateur' => $this->faker->firstName,
            'dateAnnivUti' => $this->faker->date,
        ];
    }
}