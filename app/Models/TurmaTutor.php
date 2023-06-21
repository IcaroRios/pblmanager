<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TurmaTutor extends Model
{
	protected $table = 'turma_tutors';

	protected $fillable = ['user_id','turma_id'];

	public function getCreatedAtAttribute($value){
		return Carbon::parse($value)->setTimezone(config('app.timezone'))->format('d/m/Y H:i');
	}

	public function turma()
	{
		return $this->belongsTo(Turma::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
