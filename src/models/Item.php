<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Conner\Tagging\Taggable;
use Folio;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Item extends Model implements Feedable
{
	use SoftDeletes;
	use Taggable;

	protected $table;
	protected $dates = ['deleted_at'];
	protected $softDelete = true;

	public function __construct() {
	    parent::__construct();
	    $this->table = config('folio.db-prefix').'items';
	}

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

	/**
	 * The feed representation of an Item.
	 */
	public function toFeedItem()
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary('summary')
            ->updated(new \Date($this->published_at))
            ->link('url-here')
            ->author('author');
	}
	
	public function templateView() {

		if(!$this->template) return null;

		$dir = config('folio.templates-path');
		$template_name = str_replace("/","",$this->template);
		if($this->template[0] == '/') {
			$dir = 'folio::template';
		}

		$view = $dir.'.'.$template_name;

		//if(view()->exists($view)) {
			return $view;
		//}
	}

	public function path() {
		if($this->slug[0] == "/") {
			return substr($this->slug, 1, strlen($this->slug)-1);
		}
		return Folio::path().$this->slug;
	}

	public function prev() {
			if($prev = Item::where('published_at','<', $this->published_at)->
											 orderBy('published_at', 'DESC')->
											 first()) {
				return $prev;
			}
			return Item::orderBy('published_at', 'DESC')->
									 first();
	}

	public function next() {
			if($next = Item::where('published_at','>', $this->published_at)->
											 orderBy('published_at', 'ASC')->
											 first()) {
				return $next;
			}
			return Item::orderBy('published_at', 'ASC')->
									 first();
	}

	public function prevWithAnyTag($tags) {
			if($prev = Item::withAnyTag($tags)->
											 where('published_at','<', $this->published_at)->
											 orderBy('published_at', 'DESC')->
											 first()) {
				return $prev;
			}
			return Item::withAnyTag($tags)->
									 orderBy('published_at', 'DESC')->
									 first();
	}

	public function nextWithAnyTag($tags) {
			if($next = Item::withAnyTag($tags)->
											 where('published_at','>', $this->published_at)->
											 orderBy('published_at', 'ASC')->
											 first()) {
				return $next;
			}
			return Item::withAnyTag($tags)->
									 orderBy('published_at', 'ASC')->
									 first();
	}

	public function recipients()
	{
		return $this->hasMany('Recipient');
	}

	public function properties()
	{
		return $this->hasMany('Property');
	}

	public function property($key) {
		if($property = $this->properties()->where('name', $key)->first()) {
				if($value = $property->value) {
					// property exists and has value
					return $property;
				} else {
					// property exists, but has no value
				}
		} else {
			// property with $key does not exist in database
		}
		return NULL;
	}

	public function scopeBlog($query)
	{
		return $query->where('is_blog', '=', 1);
	}

	public function scopePublic($query)
	{
		return $query->has('recipients', '=', 0);
	}

	public function scopeRSS($query)
	{
		return $query->where('rss', '=', 1);
	}

	public function scopePublished($query)
	{
		return $query->where('published_at', '<', date("Y-m-d H:i:s"));
	}

	public function scopeExpected($query)
	{
		return $query->where('published_at', '>', date("Y-m-d H:i:s"));
	}

	public function scopeVisibleFor($query, $handle)
	{
		$query->public()
    		  ->orWhereHas('recipients', function($q) use ($handle)
    			{
					$q->where("twitter", "=", $handle);
    			});
	}

	// Helpers to Check Visibility of Private Content

	public function visibleFor($twitter_handle) {
		$twitter_handle = strtolower(str_replace("@", "", $twitter_handle));
		if(in_array($twitter_handle, $this->recipientsArray() )) {
			return true;
		} else {
			return false;
		}
	}

	public function recipientsArray() {
		return explode(",", strtolower(str_replace([" ", "@"], "", $this->recipients_str)));
	}

	public function isPublic() {
		return !count($this->recipients()->get());
	}

	// TODO: Add tests (https://websanova.com/blog/laravel/creating-a-new-package-in-laravel-5-part-5-unit-testing)
	/**
	 * Retrieve the thumbnail corresponding to this image
	 */
	public function thumbnail($forceAbsolute = true) {

			$thumbnail = $this->image_src;
			
            // Fall back to image, video, or default image_src
            if($this->image_src == '') {
				if($this->image) {
					$thumbnail = $this->image;
				} else if ($this->video) {
					$thumbnail = \Thinker::getVideoThumb($this->video);
				} else {
					$thumbnail = config('folio.image-src');
				}
			} 

			// Make path absolute (add domain) when thumbnail is relative
			if($thumbnail && $forceAbsolute && substr($thumbnail, 0, 1) == '/') {
				return \Request::root().$thumbnail;
			} else {
				return $thumbnail;
			}
	}
	
}
