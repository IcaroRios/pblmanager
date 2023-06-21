<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disciplina extends Model
{
	use SoftDeletes;

	protected $table = 'disciplinas';

	protected $fillable = ['code','name','workload','departamento_id','folder_id'];

	public function departamento()
	{
		return $this->belongsTo(Departamento::class);
	}

	public function disciplina_ofertadas()
	{
		return $this->hasMany(DisciplinaOfertada::class);
	}
}
