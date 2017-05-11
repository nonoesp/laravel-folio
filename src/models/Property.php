<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
	protected $table;
	protected $fillable = array('id', 'item_id', 'label', 'name', 'value');

	public function __construct() {
	    parent::__construct();
	    $this->table = config('folio.db-prefix').'item_properties';
	}

	public function item()
	{
		return $this->hasOne('Item');
	}
}
