<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capitaine extends Model
{
    use HasFactory;
    protected $table = 'capitaine';
    protected $primaryKey = 'idCapitaine';
    protected $fillable = ['nomCapitaine', 'prenomCapitaine', 'dateAnnivCapi', 'identifiant', 'password'];
}
