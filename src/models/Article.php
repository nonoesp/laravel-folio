<?php namespace Nonoesp\Writing;

use \Illuminate\Database\Eloquent\SoftDeletingTrait;

class Article extends \Eloquent {
	use \SoftDeletingTrait;
	use \Conner\Tagging\TaggableTrait;

	protected $dates = ['deleted_at'];
	protected $softDelete = true;

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
