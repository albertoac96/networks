<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repositorio extends Model
{
    use HasFactory;
    protected $table = 'repositorios';
    protected $primaryKey = 'idRepo';
    protected $fillable = [
       'idColeccion',
        'uuid',
        'iPublic',
        'cNombre',
        'cDescripcion',
        'cEstatus',
        'idUsrAlta'
    ];
}
