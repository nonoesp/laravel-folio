<?php namespace Nonoesp\Space\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
	protected $table = 'space_subscribers';
	protected $fillable = array('id', 'email', 'name', 'source');
}
