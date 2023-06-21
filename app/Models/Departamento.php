<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
	use SoftDeletes;

	protected $table = 'departamentos';

	protected $fillable = ['name','abbreviation'];

	public function disciplinas()
	{
		return $this->hasMany(Disciplina::class);
	}
}
