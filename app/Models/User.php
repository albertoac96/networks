<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cAvatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getAllPermissionsAttribute() {
        $permissions = [];

       
        $idRole = DB::select("select role_id from model_has_roles where model_id = ".Auth::user()->id);
        $idRole = $idRole[0]->role_id;

        $permisosdb = DB::select("select T2.name
        from role_has_permissions as T1
        left join permissions as T2 on T1.permission_id = T2.id
        where role_id = ".$idRole);
        
        $cPermisos = "";
          foreach ($permisosdb as $permission) {
            $permiso = $permission->name;
            $cPermisos = $cPermisos." ".$permiso;
          }
          return $cPermisos;
      }


}
