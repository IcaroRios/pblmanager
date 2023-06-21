<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NotaProduto extends Model
{
	protected $fillable = ['produto_id','grade'];
}
