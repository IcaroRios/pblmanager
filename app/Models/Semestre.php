<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semestre extends Model
{
	use SoftDeletes;

	protected $table = 'semestres';

	protected $fillable = ['code','start_date','end_date'];

	public function disciplina_ofertadas()
	{
		return $this->hasMany(DisciplinaOfertada::class);
	}
}
