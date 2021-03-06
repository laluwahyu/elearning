<?php

namespace Model;

use Cartalyst\Sentinel\Roles\RoleInterface;

class User extends \Cartalyst\Sentinel\Users\EloquentUser
{
    protected $connection = 'user';

    public static function boot()
    {
        parent::boot();

        User::created(function ($user) {
            if ($user->profile) {
                // 
            } else {
                $profile                = new Profile;
                $profile->first_name    = $user->roles()->first() ? $user->roles()->first()->name : '';
                $profile->tempat_lahir  = 'Yogyakarta';
                $profile->tanggal_lahir = '1945-08-17';
                $profile->desa_id       = '34.71.11.1001';
                $profile->user_id       = $user->id;
                $profile->save();
            }
        });
    }
    
    public function getFullNameAttribute()
    {
        return $this->profile->full_name;
    }

    public function getAvatarAttribute()
    {
        return $this->profile->avatar ? home_url(PATH_AVATAR.'/'.$this->profile->avatar) : asset('images/default_avatar_male.jpg');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function inRole($role)
    {
        $roled = $this->roles->filter(function ($instance) use ($role) {
            if (is_array($role)) {
                foreach ($role as $slug_or_name) {
                    if ($instance->getRoleId() == $slug_or_name || $instance->getRoleSlug() == $slug_or_name) {
                        return true;
                    }
                }

                return false;
            }

            if ($role instanceof RoleInterface) {
                return ($instance->getRoleId() === $role->getRoleId());
            }

            if ($instance->getRoleId() == $role || $instance->getRoleSlug() == $role) {
                return true;
            }

            return false;
        });

        return $roled->count();
    }
}