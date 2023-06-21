<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemaObjetivo extends Model
{
    protected $table = "objetivo_problemas";

    protected $fillable = ['problema_id', 'title', 'description'];

    public function problema()
	{
		return $this->belongsTo(Problema::class);
	}
}
