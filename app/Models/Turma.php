<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
	protected $table = 'turmas';

	protected $fillable = ['code','disciplina_ofertada_id','class_days','class_time','folder_id'];

	public function disciplina_ofertada()
	{
		return $this->belongsTo(DisciplinaOfertada::class, 'disciplina_id');
	}

	public function turma_tutors()
	{
		return $this->hasMany(TurmaTutor::class);
	}
}
