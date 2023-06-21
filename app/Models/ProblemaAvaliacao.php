<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemaAvaliacao extends Model
{
    protected $table = "avaliacao_problemas";

    protected $fillable = ['problema_unidade_id', 'feedback', 'barema_id', 'aluno_id'];

    public function problema_unidade()
	{
		return $this->belongsTo(ProblemaUnidade::class);
	}

    public function barema(){
        return $this->belongsTo(Barema::class);
    }

    public function aluno(){
        return $this->belongsTo(User::class, 'aluno_id');
    }
}
