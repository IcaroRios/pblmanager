<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barema extends Model
{
	use SoftDeletes;

	protected $table = 'baremas';

	protected $fillable = ['name', 'problema_id'];

	public function problema(){
		return $this->belongsTo(Problema::class, 'problema_id');
	}

	public function item_baremas()
	{
		return $this->hasMany(ItemBarema::class);
	}
}
