<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TurmaAluno extends Model
{
	protected $table = 'turma_alunos';

	protected $fillable = ['user_id','turma_id'];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function turma()
	{
		return $this->belongsTo(Turma::class, 'turma_id');
	}
}
