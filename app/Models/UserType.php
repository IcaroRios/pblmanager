<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
	protected $table = 'user_types';

	protected $fillable = [
		'type'
	];
}
