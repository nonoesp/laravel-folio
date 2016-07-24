<?php namespace Nonoesp\Writing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
	protected $table = 'articles_recipients';
	protected $fillable = array('id', 'article_id', 'twitter');

	public function article()
	{
		return $this->hasOne('Article');
	}
}