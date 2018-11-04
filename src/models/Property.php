<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Property extends Model implements Sortable
{
	use SortableTrait;

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