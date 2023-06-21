<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{

    use SoftDeletes;

	protected $table = 'users';

	protected $fillable = ['username','password','email','enrollment','user_type','first_name','surname'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

	public function baremas()
	{
		return $this->hasMany(Barema::class, 'tutor_id');
	}

	public function turma_tutors()
	{
		return $this->hasMany(TurmaTutor::class);
	}
}
