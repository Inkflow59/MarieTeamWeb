<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    use HasFactory;
    protected $table = 'administrateur';
    protected $primaryKey = 'idAdmin';
    protected $fillable = ['pseudo', 'emailAdmin', 'mdp'];
}