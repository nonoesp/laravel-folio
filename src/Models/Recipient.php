<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
	protected $table;
	protected $fillable = array('id', 'item_id', 'twitter');

	public function __construct() {
	    parent::__construct();
	    $this->table = config('folio.db-prefix').'item_recipients';
	}

	public function item()
	{
		return $this->hasOne('Item');
	}
}
