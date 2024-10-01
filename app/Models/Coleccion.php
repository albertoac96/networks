<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coleccion extends Model
{
    use HasFactory;
    protected $table = 'colecciones';
    protected $primaryKey = 'idColeccion';
    protected $fillable = [
       'idProject',
        'uuid',
        'iPublic',
        'cNombre',
        'cDescripcion',
        'cEstatus',
        'idUsrAlta'
    ];
}
