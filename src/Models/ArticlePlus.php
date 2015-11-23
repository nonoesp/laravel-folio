<?php namespace Nonoesp\Writing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Conner\Tagging\Taggable;

class ArticlePlus extends Model
{
	use SoftDeletes;
	use Taggable;

	protected $dates = ['deleted_at'];
	protected $softDelete = true;

	protected $table = 'articles';

	//TODO: Make it softDelete
	//TODO: set scopeActive is_active

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	//protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = array('is_', 'remember_token');

	public static function sayHi() {
		echo "Hi, this is ArticlePlus.";
	}

	public function scopePublic($query)
	{
		return $query->where('is_public', 1);
	}

	public function scopePublished($query)
	{
		return $query->where('published_at', '<', date("Y-m-d H:i:s"));
	}

	public function scopeExpected($query)
	{
		return $query->where('published_at', '>', date("Y-m-d H:i:s"));
	}	
}