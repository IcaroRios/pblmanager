<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemaUnidade extends Model
{
    protected $table = "problema_unidades";

    protected $fillable = ['disciplina_ofertada_id', 'problema_id', 'data_entrega'];

    public function problema()
	{
		return $this->belongsTo(Problema::class);
	}

    public function disciplina_ofertada()
	{
		return $this->belongsTo(DisciplinaOfertada::class);
	}
}
