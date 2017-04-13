<?php namespace Nonoesp\Space\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
	protected $table;
	protected $fillable = array('id', 'email', 'name', 'source');
	protected $softDelete = true;

	public function __construct() {
	    parent::__construct();
	    $this->table = config('space.db-prefix').'subscribers';
	}
}
