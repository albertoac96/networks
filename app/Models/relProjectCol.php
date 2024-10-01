<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class relProjectCol extends Model
{
    use HasFactory;
    protected $table = 'rel_project_coleccion';
    protected $primaryKey = 'idRel';
    protected $fillable = [
       'idUsrAlta',
        'idColeccion',
        'idProject',
    ];
}
