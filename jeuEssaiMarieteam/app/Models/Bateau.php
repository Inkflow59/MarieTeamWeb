<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    use HasFactory;
    protected $table = 'bateau';
    protected $primaryKey = 'matricule';
    protected $fillable = ['matricule', 'modele', 'marque', 'capaciteHumaine', 'capaciteVehicule'];
}

