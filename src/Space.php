<?php namespace Nonoesp\Space;

 use Lang;
 use Config;
 use Request;
 use Html;
 use DB;
 use Item, Property; // Must be defined in your aliases
 use Form;
 use Thinker;

class Space {

	public static function itemCategoryClass($item, $class) {

		$categories = Config::get('space.special-tags'); //TODO: lang in package
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
			$result .= Space::tagWithClass($tag, $class);
			$idx++;
		}
		return $result;
	}

	public static function tagWithClass($tag, $class) {
		return Html::link(Space::path().'tag/'.$tag->slug, $tag->name, array('class' => $class));
	}

	public static function userURL($user) {
		return '/@'.$user->twitter;
	}

    /*
 	 * Returns
 	 * - path string if existing, e.g. "space"
 	 * - false if non-existing
 	 */

	public static function pathOrFalse() {
		if($path = Config::get('space.path-prefix')) {
			return $path;
		} else {
			return false;
		}
	}

	/*
	 * Returns a string either with:
	 * - path and a slash, e.g. "space/"
	 * - empty string, i.e. ""
	 */
	public static function path() {
		if($path = Space::pathOrFalse()) {
			return $path.'/';
		} else {
			return '';
		}
	}

	/*
	 * Returns a string with the admin path.
	 */

	public static function adminPath() {
		return Config::get('space.admin-path-prefix').'/';
	}

	public static function isAvailableURI() {
		if(!in_array(Request::path(), Config::get('space.protected_uris'))) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Returns true when the current URI belongs to the package or not.
	 */

	public static function isSpaceURI() {

		$path = Request::path();
		$slug = $path;

		if($space_path = Space::path()) {

			// Config has path-prefix
			$URIContainsSpacePathPrefix = count(explode($space_path, $path)) - 1;
			if($URIContainsSpacePathPrefix) {
				$slug = str_replace($space_path, "", $path);
			} else {
				return false;
			}

			if($item = DB::table('space_items')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}

		} else {

			// Config doesn't have path-prefix
			if($item = DB::table('space_items')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

  public static function itemPropertyArray($item) {
    $properties = config('space.properties');
    $item_properties = [];
    foreach($item->tags as $tag) {
      if(array_key_exists ( $tag->slug, $properties )) {
        $item_properties += $properties[$tag->slug];
      }
    }
    return $item_properties;
  }

  public static function itemPropertyFields($item) {
    foreach(Space::itemPropertyArray($item) as $key=>$value) {
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

  public static function itemPropertiesWithPrefix($item, $prefix) {
    $matching_properties = [];
    foreach($item->properties()->get() as $property) {
      if (substr($property->name, 0, strlen($prefix)) === $prefix) {
        array_push($matching_properties, $property);
      }
    }
    return $matching_properties;
  }

  public static function templates() {

    echo '<br><br>';

    $templates = [];
    $templates[null] = ucwords(strtolower('default template'));

    $template_paths = [];

    $custom_template_views_folder = ['Custom' => config('space.templates-path')];

    // Get template full paths
    // if($config_paths = config('space.custom-template-views-foldername')) {
    if($config_paths = $custom_template_views_folder) {
      foreach($config_paths as $name=>$folder) {
        //$name.' · resources/views/'.$dir
        $template_paths[$name] = [
          'folder' => $folder,
          'path' => resource_path().'/views/'.$folder,
        ];
      }
    }

    //vendor/nonoesp/space/resources/views/template
    $template_paths['Space'] = [
      'folder' => '/template',
      'path' => base_path().'/vendor/nonoesp/space/resources/views/template',
    ];

    //Get template files from directories
    foreach($template_paths as $name=>$dir) {
      $files = [];
      foreach(Thinker::filesFrom($dir['path']) as $key=>$file) {
        $template_name = str_replace('.blade.php','',$file);
        if($template_name[0] == '_') continue;
        $template_id = $template_name;
        if($name == 'Space') $template_id = '/'.$template_id;
        $template_dir_name = $dir['folder'].'/'.$template_name;
        $files[$template_id] = ucwords(strtolower(str_replace("-"," ",$template_name).' template'));//.' · '.$template_id;
      }
      if(count($files)) {
        $templates[$name] = $files;
      }
    }

    return $templates;
  }

}
