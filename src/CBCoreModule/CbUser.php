<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

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
        return \DB::table('cms_privileges')->where('id', $this->id_cms_privileges)->first();
    }

    public function myPhoto() : string
    {
        $photo =  $this->photo ?: 'vendor/crudbooster/avatar.jpg';
        return asset($photo);
    }

}
