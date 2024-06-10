<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grafo extends Model
{
    use HasFactory;
    protected $table = 'grafos';
    protected $primaryKey = 'idGrafo';
    protected $fillable = [
       'cContenido',
        'idProyecto',
        'ControlValues',
        'MeanControl',
        'RelativeAssy'
    ];
}
