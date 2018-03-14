<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Folio;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Item extends Model implements Feedable
{
	use \Mpociot\Versionable\VersionableTrait;
	use SoftDeletes;
	use \Conner\Tagging\Taggable;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var array
	 */
	protected $dates = ['deleted_at'];
	
	/**
	 * @var boolean
	 */
	protected $softDelete = true;
	
	/**
	 * @var array
	 */	
	protected $dontVersionFields = [
		'image',
		'image_src',
		'video',
		'tags_str',
		'slug',
		'slug_title',
		'link',
		'template',
		'visits',
		'recipients_str',
		'rss',
		'is_blog'
	];

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

	// The public path of the item
	// (Returns 404 if the post is hidden or scheduled for the future)
	public function path() {
		if($this->slug[0] == "/") {
			return substr($this->slug, 1, strlen($this->slug)-1);
		}
		return Folio::path().$this->slug;
	}

	// An encoded path that provides access to hidden items
	public function encodedPath($absolute = false) {
		$path = '/e/'.\Hashids::encode($this->id);
		if($absolute) {
			return \Request::root().$path;
		}
		return $path;
	}

	// The admin path to destroy this item
	public function destroyPath() {
		return '/'.config('folio.admin-path-prefix').'/item/destroy/'.$this->id;
	}

	// The admin path to destroy this item
	public function forceDeletePath() {
		return '/'.config('folio.admin-path-prefix').'/item/force-delete/'.$this->id;
	}	

	// The admin path to edit this item
	public function editPath() {
		return '/'.config('folio.admin-path-prefix').'/item/edit/'.$this->id;
	}

	// The admin path to review the history versions of this item
	public function versionsPath() {
		return '/'.config('folio.admin-path-prefix').'/item/versions/'.$this->id;
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

    /**
     * Get the Property from an Item if it exists.
	 * Returns the first one if multiple exist.
     *
	 * @param string $key
     * @return \Nonoesp\Folio\Models\Property
     */
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

    /**
     * Get an array of properties from an Item if they exist.
	 * Returns the first one if multiple exist.
     *
	 * @param string $key
     * @return \Nonoesp\Folio\Models\Property array
     */
	public function propertyArray($key) {

		$propertyArray = $this->properties()->where('name', $key)->get();
		if($propertyArray->count()) {
		  return $propertyArray;
		}
		return NULL;

	}

	// Cast the value of a property to a boolean
	// returns true if property value is 'true', false otherwise
	public function boolProperty($key) {
		if($p = $this->property($key)) {
			if($p->value == 'true') {
				return true;
			}
		}
		return false;
	}

	public function stringProperty($key, $default = null) {
		if($p = $this->property($key)) {
			if($p->value != '') {
				return $p->value;
			}
		}
		return $default;
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

		// Fallback on images to grab the main thumbnail
		if($this->image_src) {
			$thumbnail = $this->image_src;
		} else if($this->image) {
			$thumbnail = $this->image;
		} else if ($this->customVideoThumbnail()) {
			$thumbnail = $this->customVideoThumbnail();
		} else if($this->video) {
			$thumbnail = $this->videoThumbnail();
		} else {
			$thumbnail = config('folio.image-src');
		}

			// Make path absolute (add domain) when thumbnail is relative
			if($thumbnail && $forceAbsolute && substr($thumbnail, 0, 1) == '/') {
				return \Request::root().$thumbnail;
			} else {
				return $thumbnail;
			}
	}

	/**
	 * Returns the URL of the video thumbnail from the provider.
	 */
	public function videoThumbnail() {
		if($this->video) {
			return \Thinker::getVideoThumb($this->video);
		}
		return null;
	}

	/**
	 * Returns the URL of the custom video thumbnail specified
	 * on this Item with the custom property video-thumbnail.
	 */	
	public function customVideoThumbnail($forceAbsolute = true) {
		$thumbnail = null;
		if($vt = $this->stringProperty('video-thumbnail')) {
			$thumbnail = $vt;
		}			
		// Make path absolute (add domain) when thumbnail is relative
		if($thumbnail && $forceAbsolute && substr($thumbnail, 0, 1) == '/') {
			return \Request::root().$thumbnail;
		} else {
			return $thumbnail;
		}
	}

	/**
	 * Render video as HTML if the Item has a video URL.
	 * (Currently YouTube and Vimeo are supported.)
	 */
	public function renderVideo() {
		if($this->video) {
			return \Thinker::videoWithURL($this->video, 'c-item-v2__cover-media', $this->customVideoThumbnail());
		}
	}
	
}
