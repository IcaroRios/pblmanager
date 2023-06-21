<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
	protected $table = 'system_logs';

	protected $fillable = ['log'];
}
