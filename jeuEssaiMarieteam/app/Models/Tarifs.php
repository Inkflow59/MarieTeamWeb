<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

    protected $table = 'tarifs';

    protected $fillable = [
        'prix',
        'type',
        'idPeriode',
        'codeLiaison'
    ];
}