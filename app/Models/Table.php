<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $table = 'project_rel_table';
    protected $primaryKey = 'idTable';
    protected $fillable = [
       'idProject',
        'aTable',
        'aSingleTable',
        'aNodes',
        'aHeaders'
    ];
}
