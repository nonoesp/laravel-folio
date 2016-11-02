<?php namespace Nonoesp\Writing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
	protected $table = 'articles_properties';
	protected $fillable = array('id', 'article_id', 'name', 'value');

	public function article()
	{
		return $this->hasOne('Article');
	}
}
