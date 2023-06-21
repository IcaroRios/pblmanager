<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presenca extends Model
{
    protected $table = 'presencas';

	protected $fillable = ['user_id','session_id','present'];

	public function sessao()
	{
		return $this->belongsTo(Sessao::class, 'session_id');
	}
}
