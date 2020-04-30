<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use \Mpociot\Versionable\VersionableTrait;

class Property extends Model implements Sortable
{
	use VersionableTrait;
	use SortableTrait;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var array
	 */
	protected $fillable = array('id', 'item_id', 'label', 'name', 'value');

	/**
	 * @var array
	 */	
	protected $dontVersionFields = [
		'id',
		'item_id',
		'created_at',
		'updated_at',
		'order_column',
	];

	public function __construct() {
	    parent::__construct();
	    $this->table = config('folio.db-prefix').'item_properties';
	}

	public function item()
	{
		return $this->hasOne('Item');
	}

	// spatie/sortable
	public function buildSortQuery() {
		return static::query()->where('item_id', $this->item_id);
	}	
}