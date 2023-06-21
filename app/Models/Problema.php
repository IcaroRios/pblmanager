<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Problema extends Model
{
	use SoftDeletes;

    protected $fillable = ['title', 'description', 'body', 'file_path'];

    public function avaliacao()
	{
		return $this->hasMany(ProblemaAvaliacao::class, 'problema_id');
	}

    public function objetivo()
	{
		return $this->hasMany(ProblemaObjetivo::class, 'problema_id');
	}

    public function produto()
	{
		return $this->hasMany(ProblemaProduto::class, 'problema_id');
	}

    public function requisito()
	{
		return $this->hasMany(ProblemaRequisito::class, 'problema_id');
	}

    public function unidade()
	{
		return $this->hasMany(ProblemaUnidade::class, 'problema_id');
	}
}
