<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemBarema extends Model
{
	use SoftDeletes;

	protected $table = 'item_baremas';

	protected $fillable = ['barema_id','amount','name'];

	public function barema()
	{
		return $this->belongsTo(Barema::class);
	}
}
