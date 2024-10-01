<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class relColRepo extends Model
{
    use HasFactory;
    protected $table = 'rel_coleccion_repo';
    protected $primaryKey = 'idRel';
    protected $fillable = [
       'idUsrAlta',
        'idColeccion',
        'idRepo',
    ];
}
