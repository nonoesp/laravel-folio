<?php namespace Nonoesp\Writing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
	protected $table = 'items_recipients';
	protected $fillable = array('id', 'item_id', 'twitter');

	public function item()
	{
		return $this->hasOne('Item');
	}
}
