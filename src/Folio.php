<?php namespace Nonoesp\Folio;

 use Lang;
 use Config;
 use Request;
 use Html;
 use DB;
 use Item, Property; // Must be defined in your aliases
 use Form;
 use Thinker;
 use Illuminate\Support\Str;

class Folio {

	public static function itemCategoryClass($item, $class) {

		$categories = config('folio.special-tags'); //TODO: lang in package
    if($categories == '') return;

		foreach($item->tagNames() as $tag) {
			$tag = strtolower($tag);
			if (in_array($tag, $categories)) {
				echo "";
				return $class.'--'.$tag.' ';
			}
		}
		return;
	}

	public static function tagListWithItemAndClass($item, $class) {
		$result = '';
		$idx = 0;
		foreach($item->tags as $tag) {
			$result .= Folio::tagWithClass($tag, $class);
			$idx++;
		}
		return $result;
	}

	public static function tagWithClass($tag, $class) {
		return Html::link(Folio::path().'tag/'.$tag->slug, $tag->name, array('class' => $class));
	}

	public static function userUrl($user) {
		return '/@'.$user->twitter;
	}

    /*
 	 * Returns
 	 * - path string if existing, e.g. "folio"
 	 * - false if non-existing
 	 */

	public static function pathOrFalse() {
		if($path = config('folio.path-prefix')) {
			return $path;
		} else {
			return false;
		}
	}

	/*
	 * Returns a string either with:
	 * - path and a slash, e.g. "folio/"
	 * - empty string, i.e. ""
	 */
	public static function path() {
		if($path = Folio::pathOrFalse()) {
			return $path.'/';
		} else {
			return '';
		}
	}

	/*
	 * Returns a string with the admin path.
	 */

	public static function adminPath($path = null) {
        $adminPath = config('folio.admin-path-prefix').'/';
        return $path ? $adminPath.$path : $adminPath;
	}

	public static function isReservedURI($uri = null) {
    if (!$uri) {
      $uri = Request::path();
    }
    $reserved_uris = config('folio.reserved-uris');
		if($reserved_uris && in_array($uri, config('folio.reserved-uris'))) {
			return true;
		}
    return false;
	}

	/*
	 * Whether the current path is an Item redirection.
	 */  
  public static function pathIsItemRedirection() {

    // Abort when there's no access to Items database table
    if (\Schema::hasTable(Folio::table('item_properties')) == false) {
      return false;
    }

    // Get current path
    $path = Request::path();

    // Get 'item-redirection-property-name'
    $itemRedirectionPropertyName = config('folio.item-redirection-property-name');
    if(empty($itemRedirectionPropertyName)) {
      $itemRedirectionPropertyName = 'redirect';
    }

    $redirection = DB::table(Folio::table('item_properties'))->whereName($itemRedirectionPropertyName)->whereValue($path)->first();

    if ($redirection) {
      return $redirection->item_id;
    }

    return false;
  }

	/*
	 * Whether the current URI is a Folio URI. (Maybe rename URI for path?)
	 */
	public static function isFolioURI($path = null) {

    // Abort when there's no access to Items database table
    if (\Schema::hasTable(Folio::table('items')) == false) {
      return false;
    }

    if ($path) {
      // Remove first / if provided
      if ($path[0] == '/') {
        $path = substr($path, 1, strlen($path) - 1);
      }
    } else {
      // Get current path
      $path = Request::path();
    }

    // Look for Items with current slug
		if($folio_path = Folio::path()) {
      // path-prefix set in config
			if(substr($path,0,strlen($folio_path)) == $folio_path) {
        // {path-prefix}/{slug}
        // path-prefix != ''
				$slug = str_replace($folio_path, "", $path);
        if($item = DB::table(Folio::table('items'))->whereSlug($slug)->first()) {
  				return true;
        }
			}
		} else if($item = DB::table(Folio::table('items'))->whereSlug($path)->first()) {
      // {slug}
      // path-prefix == ''
			return true;
		}
    if($item = DB::table(Folio::table('items'))->whereSlug('/'.$path)->first()) {
      // search for absolute explicit slug
      return true;
    }
		return false;
	}

  // TODO: Place inside Item class
  public static function itemPropertyArray($item) {
    $properties = config('folio.properties');
    $item_properties = [];
    foreach($item->tags as $tag) {
      if(array_key_exists ( $tag->slug, $properties )) {
        $item_properties += $properties[$tag->slug];
      }
    }
    return $item_properties;
  }

  // TODO: Place inside Item class
  public static function itemPropertyFields($item) {
    foreach(Folio::itemPropertyArray($item) as $key=>$value) {
      $placeholder = $key;
      if(is_string($value)) {
        $placeholder = $value;
      } else if (is_array($value)) {
        if(array_key_exists('placeholder', $value)) {
          $placeholder = $value['placeholder'];
        }
      }
      $property = Property::where(['item_id' => $item->id, 'name' => $key])->first();
      $value = "";
      if($property && $property->value) {
        $value = $property->value;
      }
      echo '<p>'.Form::text($key, $value, array('placeholder' => $placeholder)).'</p>';
    }
  }

  // DONE: Place inside Item class
  public static function itemPropertiesWithPrefix($item, $prefix) {
    return $item->itemPropertiesWithPrefix($prefix);
  }

  /**
   * Return the existing templates in the resources/view/template directory.
   * 
   * @return array
   */
  public static function templates() {

    $templates = [];
    $templates["null"] = ucwords(strtolower('default template'));

    $template_paths = [];

    $custom_template_views_folder = ['Custom' => config('folio.templates-path')];

    // Get template full paths
    // if($config_paths = config('folio.custom-template-views-foldername')) {
    if($config_paths = $custom_template_views_folder) {
      foreach($config_paths as $name=>$folder) {
        //$name.' · resources/views/'.$dir
        $template_paths[$name] = [
          'folder' => $folder,
          'path' => resource_path().'/views/'.$folder,
        ];
      }
    }

    //vendor/nonoesp/folio/resources/views/template
    $template_paths['Folio'] = [
      'folder' => '/template',
      'path' => base_path().'/vendor/nonoesp/folio/resources/views/template',
    ];

    //Get template files from directories
    foreach($template_paths as $name=>$dir) {
      $files = [];
      foreach(Thinker::filesFrom($dir['path']) as $key=>$file) {
        $template_name = str_replace('.blade.php','',$file);
        if($template_name[0] == '_') continue;
        $template_id = $template_name;
        if($name == 'Folio') $template_id = '/'.$template_id;
        $template_dir_name = $dir['folder'].'/'.$template_name;
        $files[$template_id] = ucwords(strtolower(str_replace("-"," ",$template_name).''));
      }
      if(count($files)) {
        $templates[$name] = $files;
      }
    }

    return $templates;
  }

  /**
   * Get the name of database table with its Folio prefix.
   */
  public static function table($name) {
    return config('folio.db-prefix').$name;
  }

  /**
   * Get the permalink prefix.
   */  
  public static function permalinkPrefix() {
    
    $permalink_prefix = '';
    
    if($prefix = config('folio.permalink-prefix')) {
			if($prefix[0] == '{' && $prefix[strlen($prefix) - 1] == '}') {
				// another Folio config key as prefix, root/key/id
				$configKey = substr($prefix, 1, strlen($prefix) - 2);
				$permalink_prefix = config('folio.'.$configKey).'/';
			} else {
				// provided string as prefix, root/prefix/id
				$permalink_prefix = $prefix.'/';
			}
    }
    
    return $permalink_prefix;
  }

  /**
   * Get the Google Analytics id corresponding to a domain.
   */
  public static function googleAnalytics(string $domain = null) {

    $gaConfig = config('folio.google-analytics');
    if (!isset($domain)) {
      $domain = Request::getHttpHost();
    }

    if ($gaConfig) {
      if (is_array($gaConfig)) {
        // array
        if (array_key_exists($domain, $gaConfig)) {
          return $gaConfig[$domain];
        }
      } else {
        // string
        return $gaConfig;
      }
    }
    return null;
  }

  /**
   * Returns the path to an upload in the 'uploader.public-folder' directory.
   */
  public static function upload($path = null) {
    $uploadsDir = config('folio.uploader.public-folder');
    if ($path) {
      return $uploadsDir.'/'.$path;
    }
    return $uploadsDir;
  }

  /**
   * Returns the URL to an upload.
   */
  public static function uploadUrl($path = null) {
    $upload = Folio::upload($path);
    return Folio::mediaUrl($upload);
  }

  public static function mediaUrl($path = '') {
    if (config('folio.imgix')) {
      return imgix($path);
    }
    return Folio::url($path);
  }

  /**
   * Returns the absolute Url of a relative path.
   */
  public static function url($path = '/') {
    if (
      Str::of($path)->isNotEmpty() &&
      !Str::of($path)->startsWith('https://') &&
      !Str::of($path)->startsWith('http://') &&
      !Str::of($path)->startsWith('//')
      ) {
        return request()->root().Str::of($path)->start('/');
      }
      return $path;
  }

  public static function protocol() {
    return request()->secure() ? 'https://' : 'http://';
  }

  public static function urlPath($url = null) {
    $root = request()->root();
    if ($url && Str::of($url)->startsWith($root)) {
      return str_replace($root, '', $url);
    }
    return $url;
  }

  /**
   * Returns the path to an asset in the 'assets-folder' directory.
   */
  public static function asset($path = null) {
    $assetsDir = config('folio.assets-folder');
    if ($path) {
      return $assetsDir.'/'.$path;
    }
    return $assetsDir;
  }

  /**
   * Returns an array of classes expanded with a base class
   * (only expanding classes thatstart with `--`).
   */
  public static function expandClasses($classes, $baseClass = '') {
    if (!$classes) {
      return [];
    }
    if (!is_array($classes)) {
      $classes = [$classes];
    }
    foreach($classes as $key => $class) {
      if (Str::of($class)->startsWith('--')) {
        $classes[$key] = $baseClass.$class;
      }
    }
    return $classes;
  }

  public static function expandClassesAsString($classes, $baseClass = '') {
    return join(" ", Folio::expandClasses($classes, $baseClass));
  }
  
  public static function trans($key = null) {
    if (!$key) {
      return null;
    }
    if (Str::of($key)->is('{*}')) {
      $key = Str::of($key)->replace(['{', '}'], ['', '']);
    }
    return trans((string)$key);
  }

}
