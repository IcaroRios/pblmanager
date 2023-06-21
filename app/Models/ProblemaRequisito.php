<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemaRequisito extends Model
{
    protected $table = "requisito_problemas";

    protected $fillable = ['problema_id', 'title', 'description'];

    public function problema()
	{
		return $this->belongsTo(Problema::class);
	}
}
