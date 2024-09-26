<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'nomUtilisateur' => $this->faker->name,
            'prenomUtilisateur' => $this->faker->name,
            'dateAnnivUtilisateur' => $this->faker->date,
            'emailUtilisateur' => $this->faker->unique()->safeEmail,
            'passwordUtilisateur' => Hash::make('password'),
        ];
    }
}