<?php namespace Nonoesp\Space\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
	protected $table = 'space_item_recipients';
	protected $fillable = array('id', 'item_id', 'twitter');

	public function item()
	{
		return $this->hasOne('Item');
	}
}
