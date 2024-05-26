<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $primaryKey = 'idProject';
    protected $fillable = [
       'idUsrAlta',
        'cName',
        'cDescription',
        'uuid',
        'cSheet',
        'cDocName'
    ];
}
