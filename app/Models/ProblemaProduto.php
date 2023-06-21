<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemaProduto extends Model
{
    protected $table = "produto_problemas";

    protected $fillable = ['problema_id', 'item_name', 'amount'];

    public function problema()
	{
		return $this->belongsTo(Problema::class);
	}
}
