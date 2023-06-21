<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sessao extends Model
{
    use SoftDeletes;

    protected $table = 'sessoes';

	protected $fillable = ['title','session_date','turma_id','problema_unidades_id'];

    public function getSessionDateAttribute($value){
		return Carbon::parse($value)->setTimezone(config('app.timezone'))->format('d/m/Y');
	}

	public function turma()
	{
		return $this->belongsTo(Turma::class, 'turma_id');
	}

	public function ProblemaUnidade()
	{
		return $this->belongsTo(ProblemaUnidade::class, 'problema_unidades_id');
	}

    public function presencas(){
        return $this->hasMany(Presenca::class, 'session_id');
    }
}
