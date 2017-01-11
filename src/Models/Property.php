<?php namespace Nonoesp\Writing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
	protected $table = 'items_properties';
	protected $fillable = array('id', 'item_id', 'label', 'name', 'value');

	public function item()
	{
		return $this->hasOne('Item');
	}
}
