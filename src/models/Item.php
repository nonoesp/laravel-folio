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
		'title',
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
	public function boolProperty($key, $default = false) {
		if($p = $this->property($key)) {
			if($p->value == 'true') {
				return true;
			} else {
				return false;
			}
		}
		return $default;
	}

	public function stringProperty($key, $default = null) {
		if($p = $this->property($key)) {
			if($p->value != '') {
				return $p->value;
			}
		}
		return $default;
	}

	public function intProperty($key, $default = null) {
		if($p = $this->property($key)) {
			if($p->value != '') {
				return intval($p->value);
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

	public static function convertToHtml($text, $markdown_parser = 'default', $veilImages = 'true') {

		//$markdown_parser = $this->stringProperty('markdown-parser', 'default');

		if($markdown_parser == "commonmark") {

			// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
			$environment = \League\CommonMark\Environment::createCommonMarkEnvironment();
			// Optional: Add your own parsers, renderers, extensions, etc. (if desired)
			$environment->addExtension(new \Webuni\CommonMark\AttributesExtension\AttributesExtension);
			// For example:  $environment->addInlineParser(new TwitterHandleParser());
			// Define your configuration (reference at https://commonmark.thephpleague.com/configuration/):
			$config = ['html_input' => 'allow'];
			// Create the converter
			$converter = new \League\CommonMark\CommonMarkConverter($config, $environment);

			// read and parse markdown
			$html = $converter->convertToHtml($text);

			$html = str_replace(
				["<p><img", "/></p>"],
				["<img",    "/>"],
				$html);

			// Replace image src for veil.gif to then show with unveil.js
			// and save the user from initially loading all images

			if($veilImages) {

				$search = array( 
					'/<img src="(.*?)" alt="(.*?)" \/>/is',
					'/<img class="(.*?)" src="(.*?)" alt="(.*?)" \/>/is'
					); 

				$replace = array( 
						'<img src="/img/veil.gif" data-src="$1" alt="$2" \/>',
						'<img class="$1" src="/img/veil.gif" data-src="$2" alt="$3" \/>'
						); 

				$html = preg_replace ($search, $replace, $html); 
			}
		
		} else if($markdown_parser == "vtalbot") {

			$html = \VTalbot\Markdown\Facades\Markdown::convertToHtml($text);
			$html = str_replace(["<p><img","/></p>"],["<img","/>"], $html);

		} else {

			$html = \Michelf\MarkdownExtra::defaultTransform($text);
			$html = str_replace(["<p><img","/></p>"],["<img","/>"], $html);

		}

		return $html;
	}

	/**
	 * Get the Item's Markdown text parsed as HTML with
	 * the correct parser. Each Item can set its own parser
	 * by specifying the custom property 'markdown-parser' to
	 * commonmark, vtalbot, or michelf
	 */
	public function htmlText($veilImages = true) {
		$markdown_parser = $this->stringProperty('markdown-parser', 'default');
		$html = Item::convertToHtml($this->text, $markdown_parser, $veilImages);
		return $html;
	}
	
	/**
	 * Get the Item's permanent link, constructed with
	 * the 'permalink-prefix' from Folio's config and
	 * the id of the Item.
	 */	
	public function permalink() {
		return \Request::root().'/'.Folio::permalinkPrefix().$this->id;
	}

	/**
	 * Get the Item's permanent link for disqus, constructed with
	 * the 'disqus/' prefix plus Item's id.
	 */	
	public function disqusPermalink() {
		return str_replace("https", "http", \Request::root().'/disqus/'.$this->id);
	}

	public function collection() {
		return Item::makeCollection([
			'tags' => $this->stringProperty('collection'),
			'sort' => $this->stringProperty('collection-sort', 'published_at'),
			'order' => $this->stringProperty('collection-order', 'DESC'),
			'limit' => $this->intProperty('collection-limit'),
			'showAll' => $this->boolProperty('collection-show-all'),
		]);
	}

	public static function arrayValueOrDefault($array, $key, $default = null) {
		if(array_key_exists($key, $array)) {
			return $array[$key];
		}
		return $default;
	}

	/**
	 * Create a collection of items with a query.
	 * @param $params
	 * 	[
	 *		'tags' => 'design, code', // or '*' for wildcard
	 *		'sort' => 'published_at',
	 *		'order' => 'DESC',
	 *		'limit' => 5,
	 *		'showAll' => false, // or true to display all items
	 *	]
	 */
	public static function makeCollection($params) {

		$tags = Item::arrayValueOrDefault($params, 'tags');
		$sort = Item::arrayValueOrDefault($params, 'sort', 'published_at');
		$order = Item::arrayValueOrDefault($params, 'order', 'DESC');
		$limit = Item::arrayValueOrDefault($params, 'limit');
		$showAll = Item::arrayValueOrDefault($params, 'showAll', false);
		$collection = [];
		$isAdmin = false;
		if ($user = \Auth::user() and $user->is_admin) {
			$isAdmin = true;
		}

    	if(isset($tags)) {
        	$tagsArray = explode(",", $tags);

          if($tags === "*") {
            
            // Show all items (tag wildcard)
            if($showAll) {
				if($limit) {
					if($isAdmin) {
						$collection = Item::withTrashed()
											->orderBy($sort, $order)
											->take($limit)
											->get();	
					} else {
						$collection = Item::orderBy($sort, $order)
											->take($limit)
											->get();	
					}
				} else {
					if($isAdmin) {
						$collection = Item::withTrahsed()
											->orderBy($sort, $order)
											->get();
					} else {
						$collection = Item::orderBy($sort, $order)
											->get();
					}
				}
            } else {
				if($limit) {
		              $collection = Item::blog()
										->orderBy($sort, $order)
										->take($limit)
										->get();					
				} else {
					   $collection = Item::blog()
					   					 ->orderBy($sort, $order)
					   					 ->get();
				}
            }

          } else {

            // Show all items with provided tags
            if($showAll) {
				if($limit) {
						if($isAdmin) {
							$collection = Item::withTrashed()->withAnyTag($tagsArray)
												->orderBy($sort, $order)
												->take($limit)
												->get();	
						} else {
							$collection = Item::withAnyTag($tagsArray)
												->orderBy($sort, $order)
												->take($limit)
												->get();	
						}				
				} else {	
					if($isAdmin) {
						$collection = Item::withTrashed()
											->withAnyTag($tagsArray)
											->orderBy($sort, $order)
											->get();			
					} else {
						$collection = Item::withAnyTag($tagsArray)
											->orderBy($sort, $order)
											->get();						
					}			
				}
            } else {
				if($limit) {
		              $collection = Item::withAnyTag($tagsArray)
										->blog()
										->orderBy($sort, $order)
										->take($limit)
										->get();					
				} else {	
              		$collection = Item::withAnyTag($tagsArray)
					  				 	->blog()
										->orderBy($sort, $order)
										->get();
				}
            }

          }
	  }
	  return $collection;
	}
}
