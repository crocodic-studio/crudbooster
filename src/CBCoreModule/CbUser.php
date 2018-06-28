<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CbUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'cms_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function role()
    {
        return \DB::table('cms_roles')->where('id', $this->cms_roles_id)->first();
    }

    public function myPhoto() : string
    {
        $photo =  $this->photo ?: 'vendor/crudbooster/avatar.jpg';
        return asset($photo);
    }

}
