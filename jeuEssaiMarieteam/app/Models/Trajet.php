<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;
    protected $table = 'trajet';
    protected $primaryKey = 'idTrajet';
    protected $fillable = ['villeDepart', 'villeArrivee', 'date', 'heureDepart', 'heureArrivee', 'tarifEnfant', 'tarifAdulte', 'tarifVoiture', 'tarifPoidsLourd', 'etat'];
}