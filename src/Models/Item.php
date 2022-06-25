<?php namespace Nonoesp\Folio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Folio;
use Thinker;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Spatie\Regex\Regex;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Item extends Model implements Feedable, Searchable
{
	use \Mpociot\Versionable\VersionableTrait;
	use SoftDeletes;
	use \Conner\Tagging\Taggable;
	use \Spatie\Translatable\HasTranslations;

	/**
	 * @var array
	 */
	public $translatable = ['title', 'text'];

	/**
	 * @var array
	 */
	public $with = ['properties', 'recipients', 'tagged'];

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

	public function getSearchResult(): SearchResult
	{
		$url = $this->path();

		return new \Spatie\Searchable\SearchResult(
		   $this,
		   $this->title,
		   $url
		);
	}

	/**
	 * The feed representation of an Item.
	 */
	public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary('TK_SUMMARY')
            ->updated(new \Date($this->published_at))
            ->link($this->Url())
            ->authorName(config('folio.feed.default-author'))
            ->authorEmail('TK.AUTHOR@EMAIL.COM')
			;
	}
	
	public function templateView() {

		if (!$this->template) {
            return null;
        }

		$dir = config('folio.templates-path');
		$template_name = str_replace("/","",$this->template);
		if($this->template[0] == '/') {
			$dir = 'folio::template';
		}

		return $dir.'.'.$template_name;
    }

	public function domain() {
		$domain = $this->stringProperty('domain', config('folio.main-domain'));
		if ($domain == null) {
			return request()->getHttpHost();
		}
		return $domain;
	}

	public function uri() {
		if (!$this->slug) {
			return null;
		}
		$uri = Folio::path().$this->slug;
		if($this->slug[0] == "/") {
			$uri = substr($this->slug, 1, strlen($this->slug)-1);
		}
		return $uri;
	}

	// The public path of the item
	// (Returns 404 if the post is hidden or scheduled for the future)
	public function path($absolute = false) {
		if (
			$absolute ||
			$this->domain() != request()->getHttpHost()
		) {
			return $this->Url();
		}
		return '/'.$this->uri();
	}

	public function sharePath($absolute = true) {
		$path = 'share/'.$this->uri();
		if (
			$absolute ||
			$this->domain() != \Request::getHttpHost()
		) {
			$path = '//'.$this->domain().'/'.$path;
		}		
		return $path;
	}

	/**
	 * The public Url of the item.
	 */
	public function Url() {
		return Folio::protocol().$this->domain().'/'.$this->uri();
	}

	// An encoded path that provides access to hidden items
	public function encodedPath($absolute = false) {
		$path = '/e/'.Folio::hashids()->encode($this->id);
		if($absolute) {
			return $this->domain().$path;
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
	public function editPath($absolute = false) {
		$path = '/'.config('folio.admin-path-prefix').'/item/edit/'.$this->id;
		if ($absolute) {
			return $this->domain().$path;
		}
		return $path;
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

	public function prevWithAnyTag($tags, $loop = true) {
			if($prev = Item::withAnyTag($tags)->
							published()->
							where('published_at','<', $this->published_at)->
							orderBy('published_at', 'DESC')->
							first()) {
				return $prev;
			} else if ($loop) {
				return Item::withAnyTag($tags)->
				published()->
				orderBy('published_at', 'DESC')->
				first();				
			}
			return;
	}

	public function nextWithAnyTag($tags, $loop = true) {
			if($next = Item::withAnyTag($tags)->
							published()->
							where('published_at','>', $this->published_at)->
							orderBy('published_at', 'ASC')->
							first()) {
				return $next;
			} else if ($loop) {
				return Item::withAnyTag($tags)->
						published()->
						orderBy('published_at', 'ASC')->
						first();
			}
			return;
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
		if($property = $this->properties->sortBy('order_column')->where('name', $key)->first()) {
				$value = $property->value;
				if($value || $value != '') {
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

		$properties = $this->properties->sortBy('order_column')->where('name', $key);
		if($properties->count()) {
		  return $properties;
		}
		return [];

	}

	public function propertiesWithPrefix($prefix) {

		$matching_properties = [];
		$currentLocale = app()->getLocale();

		foreach($this->properties->sortBy('order_column') as $property) {

			// Discard if property is localization, otherwise add
			$parts = explode("--", $property->name);
			$parts_count = count($parts);
			$last_part = $parts[$parts_count - 1];

			if ($parts_count > 1 && \Symfony\Component\Intl\Locales::exists($last_part)) {
				
				// (0) This property is a localization

			} else {

				// Not a localization
				// (1) Find localization or (2) add original property

				if (substr($property->name, 0, strlen($prefix)) === $prefix) {
					$localizedKey = $property->name.'--'.$currentLocale;
					if ($this->hasProperty($localizedKey)) {
						array_push($matching_properties, $this->property($localizedKey));
					} else {
						array_push($matching_properties, $property);
					}
				}
			}

		}

		return $matching_properties;
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

	public function hasProperty($key) {
		foreach($this->properties as $p) {
			if ($p->name === $key) return true;
		}
		return;
	}

	public function stringProperty($key, $default = null) {

		$currentLocale = app()->getLocale();
		$localizedKey = $key.'--'.$currentLocale;
		$key = $this->hasProperty($localizedKey) ? $localizedKey : $key;

		if($p = $this->property($key)) {
			if($p->value != '') {
				return $p->value;
			}
		}
		return $default;
	}

	public function intProperty($key, $default = null) {
		if($p = $this->property($key)) {
			if ($p->value != '') {
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
		if (in_array($twitter_handle, $this->recipientsArray() )) {
			return true;
		} else {
			return false;
		}
	}

	public function recipientsArray() {
		return explode(",", strtolower(str_replace([" ", "@"], "", $this->recipients_str)));
	}

	public function isPublic() {
		return !count($this->recipients);
	}

	/*

	██╗    ███╗   ███╗     █████╗      ██████╗     ███████╗
	██║    ████╗ ████║    ██╔══██╗    ██╔════╝     ██╔════╝
	██║    ██╔████╔██║    ███████║    ██║  ███╗    █████╗  
	██║    ██║╚██╔╝██║    ██╔══██║    ██║   ██║    ██╔══╝  
	██║    ██║ ╚═╝ ██║    ██║  ██║    ╚██████╔╝    ███████╗
	╚═╝    ╚═╝     ╚═╝    ╚═╝  ╚═╝     ╚═════╝     ╚══════╝
	
	*/

	public function image($imgixOptions = []) {
		return $this->imgix($this->image, $imgixOptions);
	}

	public function image_src($imgixOptions = []) {
		return $this->imgix($this->image_src, $imgixOptions);
	}

	// Lazy load images by key
	public function expandImage($key) {
		if ($key == 'VIDEO_IMAGE') {
			return $this->videoImage();
		}
		return null;
	}

	public function imageFallback($imgixOptions = [], $params = []) {
		
		$absolute = Arr::get($params, 'absolute', false);
		$fallback = Arr::get($params, 'fallback', null);

		$image = null;

		if ($fallback) {
			if (is_array($fallback)) {
				foreach($fallback as $fallback_image) {
					if ($fallback_image == 'VIDEO_IMAGE') {
						// Lazy load Vimeo thumbnails
						$fallback_image = $this->expandImage($fallback_image);
					}
					if ($fallback_image) {
						$image = $fallback_image;
						break;
					}
				}
			} else {
				$image = $fallback;
			}
		}

		if (!$image) {
			return null;
		}

		$image = $this->imgix($image, $imgixOptions);

		if ($absolute) {
			$image = Folio::url($image);
		}

		return $image;		
	}

	public function imageProperty($key = null, $imgixOptions = [], $params = []) {

		$absolute = Arr::get($params, 'absolute', false);
		$fallback = Arr::get($params, 'fallback', []);

		array_unshift($fallback, $this->stringProperty($key));

		$params = array_merge($params, [
			'absolute' => $absolute,
			'fallback' => $fallback,
		]);

		return $this->imageFallback($imgixOptions, $params);
	}	

	public function cardImage($imgixOptions = []) {

		return $this->imageFallback($imgixOptions, [
			'absolute' => false,
			'fallback' => [
				$this->stringProperty('card-image'),
				$this->image,
				$this->image_src,
				'VIDEO_IMAGE',
			]
		]);
	}

	public function feedImage($imgixOptions = []) {
		return $this->imageFallback($imgixOptions, [
			'absolute' => true,
			'fallback' => [
				$this->stringProperty('feed-image'),
				$this->stringProperty('rss-image'),
				$this->image,
				$this->image_src,
			]
		]);
	}

	/**
	 * Retrieve social preview image (i.e. Open Graph image) of this item.
	 */
	public function ogImage($imgixOptions = [], $params = []) {

		$property = Arr::get($params, 'property', 'og-image');
		$absolute = Arr::get($params, 'absolute', true);
		$fallback = Arr::get($params, 'fallback', [
			$this->stringProperty($property),
			$this->image_src,
			$this->image,
			'VIDEO_IMAGE',
			config('folio.og.image'),
		]);
		
		$params = array_merge($params, [
			'property' => $property,
			'absolute' => $absolute,
			'fallback' => $fallback,
		]);

		return $this->imageFallback($imgixOptions, $params);
	}

	/**
	 * Get Item's imgix options.
	 * 
	 * 
	 * @param array $defaultOptions
	 * @param array $overrideOptions
	 * @return array
	 */
	public function imgixOptions($defaultOptions = [], $overrideOptions = []) {
		
		// Start from $defaultOptions
		$imgixOptions = $defaultOptions;

		// Get $itemOptions
		$itemOptions = [
			'w' => $this->intProperty('imgix-w'),
			'h' => $this->intProperty('imgix-h'),
			'q' => $this->intProperty('imgix-q'),
			's' => $this->intProperty('imgix-s'),
			'fit' => $this->stringProperty('imgix-fit'),
		];
		// ..removing empty keys
		foreach ($itemOptions as $key=>$value) {
			if ($value == null) {
				unset($itemOptions[$key]);
			}
		}

		// Inject $itemOptions
		foreach ($itemOptions as $key=>$value) {
			if ($value != null) {
				$imgixOptions[$key] = $value;
			}
		}

		// Inject $overrideOptions
		foreach ($overrideOptions as $key=>$value) {
			if ($value != null) {
				$imgixOptions[$key] = $value;
			}
	}		

		return $imgixOptions;
	}

	public static function imgixOptionsFromPath($path) {
		$parts = explode("?", $path);
		array_shift($parts);
		$params = [];
		$urlParams = join('', $parts);
		if (Str::of($urlParams)->isNotEmpty()) {
			foreach(explode('&', $urlParams) as $param) {
				$param_parts = explode('=', $param);
				if (count($param_parts) < 2) {
					continue;
				}
				$param_key = $param_parts[0];
				$param_value = $param_parts[1];
				$params[$param_key] = $param_value;
			};
		}
		return $params;
	}

	/**
	 * Remove Url parameters (i.e. text after ?)
	 */
	public static function stripUrlParams($url = null) {
		$url_parts = parse_url($url);
		if (isset($url_parts['query'])) {
			return str_replace('?'.$url_parts['query'], '', $url);
		} else if (Str::endsWith($url, '?')) {
			return Str::replaceLast('?', '', $url);
		}
		return $url;
	}

	/***
	 * Creates an ImgIX Url wit the provided options or returns image path.
	 */
	public function imgix($imagePath, $imgixOptions = []) {

		$imgix_active = $this->boolProperty('imgix', config('folio.imgix'));
		$isRelativePath = Str::startsWith($imagePath, '/');

		if ($imgix_active && $isRelativePath) {
			$pathImgixOptions = Item::imgixOptionsFromPath($imagePath);
			$imgixOptions = $this->imgixOptions($imgixOptions, $pathImgixOptions);
			$imagePath = Item::stripUrlParams($imagePath);

			if (Str::endsWith($imagePath, '.gif')) {
				$imgixOptions = [];
			}

			if (!is_array($imgixOptions)) {
				$imgixOptions = [];
			}

			return imgix($imagePath, $imgixOptions);
		}
		return $imagePath;		
	}

	/**
	 * Returns the URL of the video thumbnail from the provider.
	 */
	public function videoImage($imgixOptions = []) {
		return $this->imageFallback($imgixOptions, [
			'fallback' => [
				$this->stringProperty('video-thumbnail'),
				$this->stringProperty('video-image'),
				$this->providerVideoImage(),
			]
		]);
	}

	/**
	 * Returns the URL of the video thumbnail from the provider.
	 */
	public function providerVideoImage() {
		if($this->video) {
			return Thinker::getVideoThumb($this->video);
		}
		return null;
	}

	/**
	 * Render video as HTML if the Item has a video URL.
	 * (Currently YouTube and Vimeo are supported.)
	 */
	public function renderVideo() {
		if($this->video) {
			return Thinker::videoWithURL(
				$this->video,
				'c-item-v2__cover-media',
				$this->videoImage()
			);
		}
	}

	/*
	
	████████╗    ███████╗    ██╗  ██╗    ████████╗
	╚══██╔══╝    ██╔════╝    ╚██╗██╔╝    ╚══██╔══╝
	   ██║       █████╗       ╚███╔╝        ██║   
	   ██║       ██╔══╝       ██╔██╗        ██║   
	   ██║       ███████╗    ██╔╝ ██╗       ██║   
	   ╚═╝       ╚══════╝    ╚═╝  ╚═╝       ╚═╝   
	
	*/	

	/**
	 * Parse Markdown to HTML.
	 */
	public static function convertToHtml($text, $options = []) {

		// Deconstruct options or fallback to default values
		$veilImages = Arr::get($options, 'veilImages', true);
		$parser = Arr::get($options, 'parser', 'default');
		$stripTags = Arr::get($options, 'stripTags', []);
		$parseExternalLinks = Arr::get($options, 'parseExternalLinks', false);
		$newsletterFormat = Arr::get($options, 'newsletterFormat', false);
		$absoluteUrls = Arr::get($options, 'absoluteUrls', false);

		// Strip HTML tags ↓ Before parsing
		// e.g., <norss></norss> <rss></rss> <nopodcast> </nopodcast>
		if (is_array($stripTags) && count($stripTags)) {

			// Remove tags from raw text · $text
			foreach ($stripTags as $tag) {
				$tagPattern = "/<".$tag."[^>]*>(.|\n)*?<\/".$tag.">/";
				if(Regex::match($tagPattern, $text)->hasMatch()) {
					// We wrap with <p></p> to avoid leaving an empty paragraph (because we are stripping
					// the HTML output of Markdown)
					$text = Regex::replace($tagPattern, '', $text)->result();
				}
			}
		}

		if (!in_array($parser, [
			'default',
			'commonmark',
			'vtalbot',
			'michelf',
		])) {
			$parser = 'default';
		}

		if(
			$parser == 'default' ||
			$parser == 'commonmark' ||
			$parser == 'vtalbot'
			) {

				// CommonMark

			// Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
			$environment = \League\CommonMark\Environment::createCommonMarkEnvironment();
			// Optional: Add your own parsers, renderers, extensions, etc. (if desired)
			$environment->addExtension(new \League\CommonMark\Extension\Attributes\AttributesExtension);
			$environment->addExtension(new \League\CommonMark\Extension\Footnote\FootnoteExtension);
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

				$veilPath = Folio::asset('images/veil.gif');

				$search = [
					// '/<img src="(.*?)" alt="(.*?)" \/>/is',
					'/<img(.*?)src="(.*?)" alt="(.*?)" \/>/is',
				]; 

				$replace = [
						// '<img src="'.$veilPath.'" data-src="$1" alt="$2" />',
						'<img$1src="'.$veilPath.'" data-src="$2" alt="$3" />',
				];

				$html = preg_replace ($search, $replace, $html); 
			}

			// Use imgix?
			if (config('folio.imgix')) {

				$imgixDomain = config('imgix.domain');
				$protocol = config('imgix.useHttps') ? 'https' : 'http';
				$imgixPrefix = $protocol.'://'.$imgixDomain;

				$search = [
					'/<img(.*?)src="\/(.*?)" alt="(.*?)" \/>/is',
					'/<img(.*?)src="(.*?)" data-src="\/(.*?)" alt="(.*?)" \/>/is',
					'/<source src="\/(.*?)" type="video\/mp4"(.*?)>/is',
				];

				$replace = [
					'<img$1src="'.$imgixPrefix.'/$2" alt="$3" />',
					'<img$1src="$2" data-src="'.$imgixPrefix.'/$3" alt="$4" />',
					'<source src="'.$imgixPrefix.'/$1" type="video/mp4"$2>',
				]; 

				$html = preg_replace ($search, $replace, $html);
			}

		} else if (
			$parser == "michelf"
			) {

			// TODO: deprecate? 2020.07.18
			// Michelf MarkdownExtra

			$html = \Michelf\MarkdownExtra::defaultTransform($text);
			$html = str_replace(["<p><img","/></p>"],["<img","/>"], $html);

		}

		// Strip HTML tags ↑ After parsing
		// e.g., <norss></norss> <rss></rss> <nopodcast> </nopodcast>
		if (is_array($stripTags) && count($stripTags)) {	

			// Remove tags from Markdown text · $html
			foreach ($stripTags as $tag) {
				$tagPattern = "/<".$tag."[^>]*>(.|\n)*?<\/".$tag.">/";
				if(Regex::match($tagPattern, $html)->hasMatch()) {
					// We wrap with <p></p> to avoid leaving an empty paragraph (because we are stripping
					// the HTML output of Markdown)
					$html = Regex::replace($tagPattern, '', $html)->result();
				}
			}

		}

		// Parse external links
		if ($parseExternalLinks) {
			$html = Item::parseExternalLinks($html);
		}

		// Force relative Urls to be absolute
		if ($absoluteUrls) {

			$root = request()->root();
			$html = str_replace(
				[
					'src="/',
					'href="/',
				], [
					'src="'.$root.'/',
					'href="'.$root.'/',
				],
				$html
			);

		}

		// Escape elements for newsletter
		if ($newsletterFormat) {

			$html = str_replace(
				[
					'<img',
					'<p><img',
					'/></p>',
					'<hr />',
				], [
					'<img width="100%"',
					'<p class=\"rss__img-wrapper\"><img',
					'/></p>',
					'<br />',
				],
				$html
			);

		}

		return $html;
	}

	/**
	 * Get the Item's Markdown text parsed as HTML with
	 * the correct parser. Each Item can set its own parser
	 * by specifying the custom property 'markdown-parser' to
	 * commonmark, vtalbot, or michelf
	 * 
	 * $options = [
	 * 	veilImages: boolean;
	 *  parseExternalLinks: boolean;
	 *  stripTags: string[];
	 * ]
	 */
	public function htmlTextLegacy($veilImages = true, $parseExternalLinks = false) {
		$parser = $this->stringProperty('markdown-parser', 'default');
		if($this->hasExcerptTag()) {
			$text = explode(config('folio.excerpt-tag'), $this->text)[1];
			$html = Item::convertToHtml($text, $parser, $veilImages);
		} else {
            $html = Item::convertToHtml($this->text, $parser, $veilImages);
        }
        if ($parseExternalLinks) {
            $html = Item::parseExternalLinks($html);
        }
		return $html;
	}

	/**
	 * Get the item's text as HTML.
	 */
	public function htmlText($options = []) {

		if (!$this->text) {
			return '';
		}

		// Deconstruct options or fallback to default values
		$veilImages = Arr::get($options, 'veilImages', true);
		$parseExternalLinks = Arr::get($options, 'parseExternalLinks', true);
		$stripTags = Arr::get($options, 'stripTags', []);
		$parser = Arr::get($options, 'parser', null);

		// Does the text have an excerpt to trim?
		$text = $this->text;
		if($this->hasExcerptTag()) {
			$text = explode(config('folio.excerpt-tag'), $this->text)[1];
		}
		
		// Parse Markdown to HTML
		$options = array_merge($options, [
			'parser' => $parser ?? $this->stringProperty('markdown-parser'),
			'veilImages' => $veilImages,
			'stripTags' => $stripTags,
			'parseExternalLinks' => $parseExternalLinks,
		]);
		$html = Item::convertToHtml($text, $options);

		return $html;
	}
	
	public function hasExcerpt() {
		return $this->hasMoreTag() || $this->hasExcerptTag();
	}

	public function hasMoreTag() {
		return count(explode(config('folio.more-tag'), $this->text)) > 1;
	}

	public function hasExcerptTag() {
		return count(explode(config('folio.excerpt-tag'), $this->text)) > 1;
	}
	
	public function htmlTextExcerpt($options = []) {

		// Replace item text temporarily if more-tag or excerpt-tag
		
		if($this->hasExcerpt()) {
			// Get more-tag or excerpt-tag
			$tag = config('folio.more-tag');
			if ($this->hasExcerptTag()) {
				$tag = config('folio.excerpt-tag');
			}
			// Get excerpt text
			$textExcerpt = explode($tag, $this->text)[0];
			// Remember actual item text
			$fullItemText = $this->text;
			// Replace temporarily
			$this->text = $textExcerpt;
			$excerptHtml = $this->htmlText($options);
			// Revert to actual item text
			$this->text = $fullItemText;
			// Return excerpt text as HTML
			return $excerptHtml;
		}

		// Fallback to Item full text if no more-tag or excerpt-tag were found
		return $this->htmlText($options);
    }

    /**
     * Read more call to action text. Defaults to trans('folio.read-more').
     */
    public function readMoreText($default = null) {
        return $this->stringProperty('read-more-text', $default ?? trans('folio.read-more'));
    }

    /**
     * Add target="_blank" and class="is-external" to any links
     * with an href starting on http:// or https://
     */
    public static function parseExternalLinks($html) {
        $from = [
            'href="http://',
            'href="https://',
        ];
        $to = [
            'target="_blank" class="is-external" href="http://',
            'target="_blank" class="is-external" href="https://',
        ];
        return str_replace($from, $to, $html);
    }

	/**
	 * Get the Item's permanent link, constructed with
	 * the 'permalink-prefix' from Folio's config and
	 * the id of the Item.
	 */	
	public function permalink() {
		return request()->root().'/'.Folio::permalinkPrefix().$this->id;
	}

	/**
	 * Get the Item's permanent link for disqus, constructed with
	 * the 'disqus/' prefix plus Item's id.
	 */	
	public function disqusPermalink() {
		return str_replace("https", "http", request()->root().'/disqus/'.$this->id);
	}

	/**
	 * Returns an array of all existing tag names in all items.
	 */
	public static function existingTagNames() {
		return Item::existingTags()->pluck('name')->toArray();
	}

	/**
	 * Returns an array with an Item's collection tag names.
	 */
	public function collectionTagNames() {
		if($collectionTags = $this->stringProperty('collection')) {
			$tags = Str::of($collectionTags)->explode(',')->toArray();
			foreach ($tags as $key => $tag) {
				$tags[$key] = (string) Str::of($tag)->trim()->lower();
			}
			return $tags;
		}
		return null;
	}

	/**
	 * Returns the first collection of the item.
	 * TODO: Deprecate as this can be obtained from $item->collections().
	 */
	public function collection() {
		return Item::makeCollection([
			'tags' => $this->stringProperty('collection'),
			'select' => $this->stringProperty('collection-select', '*'),
			'sort' => $this->stringProperty('collection-sort', 'published_at'),
			'order' => $this->stringProperty('collection-order', 'DESC'),
			'limit' => $this->intProperty('collection-limit'),
			'showAll' => $this->boolProperty('collection-show-all'),
			'showHidden' => $this->boolProperty('collection-show-hidden'),
			'showScheduled' => $this->boolProperty('collection-show-scheduled'),
		]);
	}

	/**
	 * Returns the labels of the item's collections.
	 */
	public function collectionLabels() {

		$collectionLabels = [];

		foreach($this->propertyArray('collection') as $collectionProperty) {
			array_push($collectionLabels, $collectionProperty->label);
		}

		return $collectionLabels;
	}

	/**
	 * Returns the item's collections.
	 */
	public function collections() {

		$collections = [];

		$index = 0;

		// Get collection properties sorted by order_column
		$collectionProperties = $this->propertyArray('collection');
	
		// Store the collection count
		$collectionCount = count($collectionProperties);
		
		if ($collectionCount) {
	
			// Get clean array with sorted properties
			$collectionProperties = Arr::divide($collectionProperties->toArray())[1];
					
			// Get properties sorted by order_column
			$properties = $this->properties->sortBy('order_column');

			// Get clean array with sorted properties
			$properties = Arr::divide($properties->toArray())[1];

			$index = 0;

			foreach($collectionProperties as $collectionProperty) {
	
				unset($collection_limit);
				unset($collection_select);
				unset($collection_sort);
				unset($collection_order);
				unset($collection_show_all);
				unset($collection_show_hidden);
				unset($collection_show_scheduled);
				
				$toIndex = count($this->properties) - 1;
				$selfIndex = array_search($collectionProperty, $properties);
	
				// Find index of last sub-item
				if ($index < $collectionCount - 1) {
					$nextCollection = $collectionProperties[$index + 1];
					$nextIndex = array_search($nextCollection, $properties);
					$toIndex = $nextIndex - 1;
				}
	
				// Get subset of properties between this collection and next
				$from = $selfIndex;
				$amount = $toIndex - $from;
				$subProperties = array_slice($properties, $selfIndex + 1, $amount);
	
				// Look for collection sub-properties
				foreach ($subProperties as $key => $p) {

					$name = $p['name'];
					$value = $p['value'];
					
					switch ($name) {
						case 'collection-limit':
							$collection_limit = $value;
							break;
						case 'collection-select':
							$collection_select = $value;
							break;
						case 'collection-sort':
							$collection_sort = $value;
							break;
						case 'collection-order':
							$collection_order = $value;
							break;
						case 'collection-show-all';
							$collection_show_all = $value == 'true';
							break;
						case 'collection-show-hidden';
							$collection_show_hidden = $value == 'true';
							break;
						case 'collection-show-scheduled';
							$collection_show_scheduled = $value == 'true';
							break;
						default:
							break;
					}
	
				}
	
				// Construct collection
				$collection = Item::makeCollection([
					'tags' => $collectionProperty['value'],
					'select' => $collection_select ?? '*',
					'sort' => $collection_sort ?? 'published_at',
					'order' => $collection_order ?? 'DESC',
					'limit' => $collection_limit ?? -1,
					'showAll' => $collection_show_all ?? false,
					'showHidden' => $collection_show_hidden ?? false,
					'showScheduled' => $collection_show_scheduled ?? false,
				]);
	
				array_push($collections, $collection);
	
				$index += 1;
			}
	
		}

		return $collections;
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

		$tags = Arr::get($params, 'tags');
		$select = Arr::get($params, 'select', '*');
		$sort = Arr::get($params, 'sort', 'published_at');
		$order = Arr::get($params, 'order', 'DESC');
		$limit = Arr::get($params, 'limit');
		$showAll = Arr::get($params, 'showAll', false);
		$showHidden = Arr::get($params, 'showHidden', false);
		$showScheduled = Arr::get($params, 'showScheduled', false);
		$collection = [];

		if (!$tags) {
			return [];
		}

		$shouldShowTrashed = false;
		if ($user = \Auth::user()) {
			$shouldShowTrashed = $user->is_admin || $showHidden;
		}

		$published = function($query) { $query->published(); };

		$select = explode(',', $select);

    	if(isset($tags)) {
        	$tagsArray = explode(",", $tags);

          if($tags === "*") {
            
            // Show all items (tag wildcard)
            if($showAll) {
				if($limit) {
					if($shouldShowTrashed) {
						$collection = Item::withTrashed()
											->select($select)
											->when(!$showScheduled, $published) // formerly ->published()
											->orderBy($sort, $order)
											->take($limit)
											->get();
					} else {
						$collection = Item::select($select)
											->when(!$showScheduled, $published) // formerly published()
											->orderBy($sort, $order)
											->take($limit)
											->get();	
					}
				} else {
					if($shouldShowTrashed) {
						$collection = Item::withTrashed()
											->select($select)
											->when(!$showScheduled, $published) // formerly ->published()
											->orderBy($sort, $order)
											->get();
					} else {
						$collection = Item::select($select)
						->when(!$showScheduled, $published) // formerly published()
											->orderBy($sort, $order)
											->get();
					}
				}
            } else {
				if($limit) {
		              $collection = Item::blog()
										  ->select($select)
										  ->when(!$showScheduled, $published) // formerly ->published()
										->orderBy($sort, $order)
										->take($limit)
										->get();					
				} else {
					   $collection = Item::blog()
											  ->select($select)
											  ->when(!$showScheduled, $published) // formerly ->published()
										   ->orderBy($sort, $order)
					   					   ->get();
				}
            }

          } else {

            // Show all items with provided tags
            if($showAll) {
				if($limit) {
						if($shouldShowTrashed) {
							$collection = Item::withTrashed()
											  ->select($select)
											  ->withAnyTag($tagsArray)
											  ->when(!$showScheduled, $published) // formerly ->published()
											  ->orderBy($sort, $order)
											  ->take($limit)
  											  ->get();	
						} else {
							$collection = Item::withAnyTag($tagsArray)
											  ->select($select)
											  ->when(!$showScheduled, $published) // formerly ->published()
											  ->orderBy($sort, $order)
											  ->take($limit)
											  ->get();	
						}				
				} else {	
					if($shouldShowTrashed) {
						$collection = Item::withTrashed()
										  ->withAnyTag($tagsArray)
										  ->select($select)
										  ->when(!$showScheduled, $published) // formerly ->published()
										  ->orderBy($sort, $order)
										  ->get();			
					} else {
						$collection = Item::withAnyTag($tagsArray)
										  ->select($select)
										  ->when(!$showScheduled, $published) // formerly ->published()
										  ->orderBy($sort, $order)
										  ->get();						
					}			
				}
            } else {
				if($limit) {
		              $collection = Item::withAnyTag($tagsArray)
										->blog()
										->select($select)
										->when(!$showScheduled, $published) // formerly ->published()
										->orderBy($sort, $order)
										->take($limit)
										->get();					
				} else {	
              		$collection = Item::withAnyTag($tagsArray)
										->blog()
										->select($select)
									  ->when(!$showScheduled, $published) // formerly ->published()
									  ->orderBy($sort, $order)
									  ->get();
				}
            }

          }
	  }
	  return $collection;
	}

	public function date($format = 'F j, Y') {
		return Item::formatDate($this->published_at, $format);
	}

	public static function formatDate($date, $format = 'F j, Y') {
		return ucWords(\Date::parse($date)->format($format));
	}

	public static function bySlug($slug) {
		if (
			$item = Item::withTrashed()
			->whereSlug($slug)
			->orWhere('slug', '/'.$slug)
			->orWhere('slug', '/'.Folio::path().$slug)
			->first()
		) {
			return $item;
		}
		return null;
	}

	public function description($length = 159) {
		if ($this->hasProperty('og-description')) {
			return trim($this->summary([
				'limit' => $length,
				'property' => 'og-description',
			]));			
		}
		// TODO - Remove and stick to either og- or meta-
		return trim($this->summary([
			'limit' => $length,
			'property' => 'meta-description',
		]));
	}

	public function summary($params = ['limit' => 159, 'property' => 'summary']) {

		$limit = Arr::get($params, 'limit', 159);
		$property = Arr::get($params, 'property', 'summary');

        if ($summary = $this->stringProperty($property)) {
            return $summary;
        }
        return Thinker::limitMarkdownText($this->htmlText([
            'stripTags' => ['rss', 'podcast', 'feed']
        ]), $limit , ['sup']);
	}	
	
	/**
	 * Get the estimated reading time of this item's text.
	 */
	public function readTime() {
		return Item::ReadingTime($this->text);
	}

	/**
	 * Get the estimated reading time of an input string.
	 * 
	 * CONFIG
	 * Can be configured by publishing mtownsend/read-time's config files.
	 * php artisan vendor:publish --provider="Mtownsend\ReadTime\Providers\ReadTimeServiceProvider" --tag="read-time-config"
	 * 
	 * LOCALIZE
	 * And localized publishing its translation files.
	 * php artisan vendor:publish --provider="Mtownsend\ReadTime\Providers\ReadTimeServiceProvider" --tag="read-time-language-files"
	 */

	public static function ReadingTime(string $text) {

		$readtime = new \Mtownsend\ReadTime\ReadTime($text);
		$readtime->setTranslation([
			// 'min' => trans('read-time::read-time.min'),
			// 'minute' => trans('read-time::read-time.minute'),
			// 'read' => trans('read-time::read-time.read'),
		]);
		return $readtime->abbreviated()
						->get();
	}

	/**
	 * Whether this item is tagged with a given tag.
	 */
	public function hasTag($tag) {
		if ($tag) {
			$sanitized_tag = Str::of($tag)->trim()->lower();
			if (in_array($sanitized_tag, $this->tagSlugs())) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Override property attributes with an array of params.
	 * e.g.
	 * [
	 *   'name' => 'new-name',
	 *   'value' => 'new-value',
	 *   'label' => 'new-label'
	 * ]
	 */
	public function setProperty($name, $params) {

		// Look for existing property by name
		// or create property if it doesn't exist
		$property = $this->properties->sortBy('order_column')->where('name', $name)->first() ?? new Property();
		
		// Edit property attributes with params

		$propertyName = Arr::get($params, 'name', $name);
		$propertyValue = Arr::get($params, 'value', $property->value);
		$propertyLabel = Arr::get($params, 'label', $property->label);
		
		if (
			$property->name != $propertyName ||
			$property->value != $propertyValue ||
			$property->label = $propertyLabel			
		) 
		{
			$property->item_id = $this->id;
			$property->name = $propertyName;
			$property->value = $propertyValue;
			$property->label = $propertyLabel;
			
			// Save
			$property->save();
		}
	}

	/**
	 * Set a property value.
	 */
	public function setPropertyValue($name, $newValue) {
		$this->setProperty($name, [
			'value' => $newValue,
		]);
	}

	/**
	 * Set a property label.
	 */
	public function setPropertyLabel($name, $newLabel) {
		$this->setProperty($name, [
			'label' => $newLabel,
		]);
	}
}