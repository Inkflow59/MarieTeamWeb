<?php

namespace Database\Factories;

use App\Models\Trajet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrajetFactory extends Factory
{
    protected $model = Trajet::class;

    public function definition()
    {
        return [
            'villeDepart' => $this->faker->city,
            'villeArrivee' => $this->faker->city,
            'date' => $this->faker->date,
            'heureDepart' => $this->faker->time,
            'heureArrivee' => $this->faker->time,
            'tarifEnfant' => $this->faker->randomFloat(2, 10, 100),
            'tarifAdulte' => $this->faker->randomFloat(2, 30, 1000),
            'tarifVoiture' => $this->faker->randomFloat(2, 100, 10000),
            'tarifPoidsLourd' => $this->faker->randomFloat(2, 1000, 100000),
            'etat' => $this->faker->randomElement(["A l'heure", "En retard", "Annulé"]),
        ];
    }
}