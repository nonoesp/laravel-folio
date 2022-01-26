<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use SoftDeletes;
	protected $table;
	protected $fillable = array('id', 'email', 'name', 'source');

	public function __construct() {
	    parent::__construct();
	    $this->table = config('folio.db-prefix').'subscribers';
	}
}
