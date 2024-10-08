<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;
    protected $table = 'utilisateur';
    protected $primaryKey = 'idUtilisateur';
    protected $fillable = ['email', 'password', 'nomUtilisateur', 'prenomUtilisateur', 'dateAnnivUti'];
    protected $hidden = ['password'];
}