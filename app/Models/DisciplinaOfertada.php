<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaOfertada extends Model
{
	use SoftDeletes;

	protected $table = 'disciplina_ofertadas';

	protected $fillable = ['disciplina_id','semestre_id','number_of_classes','folder_id'];

	public function disciplina()
	{
		return $this->belongsTo(Disciplina::class);
	}

	public function semestre()
	{
		return $this->belongsTo(Semestre::class);
	}

	public function turmas()
	{
		return $this->hasMany(Turma::class, 'disciplina_id');
	}
}
