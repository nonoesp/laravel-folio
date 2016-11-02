<?php namespace Nonoesp\Writing;

 use Lang;
 use Config;
 use Request;
 use Html;
 use DB;
 use Article, Property; // Must be defined in your aliases
 use Form;

class Writing {

	public static function articleCategoryClass($tags, $class) {

		$categories = Config::get('writing.special-tags'); //TODO: lang in package

		foreach($tags as $tag) {
			$tag = strtolower($tag);
			if (in_array($tag, $categories)) {
				echo "";
				return $class.'--'.$tag.' ';
			}
		}
		return;
	}

	public static function tagListWithArticleAndClass($article, $class) {
		$result = '';
		$idx = 0;
		foreach($article->tags as $tag) {
			$result .= Writing::tagWithClass($tag, $class);
			$idx++;
		}
		return $result;
	}

	public static function tagWithClass($tag, $class) {
		return Html::link(Writing::path().'tag/'.$tag->slug, $tag->name, array('class' => $class));
	}

	public static function userURL($user) {
		return '/@'.$user->twitter;
	}

    /*
 	 * Returns
 	 * - path string if existing, e.g. "writing"
 	 * - false if non-existing
 	 */

	public static function pathOrFalse() {
		if($path = Config::get('writing.path-prefix')) {
			return $path;
		} else {
			return false;
		}
	}

	/*
	 * Returns a string either with:
	 * - path and a slash, e.g. "writing/"
	 * - empty string, i.e. ""
	 */
	public static function path() {
		if($path = Writing::pathOrFalse()) {
			return $path.'/';
		} else {
			return '';
		}
	}

	/*
	 * Returns a string with the admin path.
	 */

	public static function adminPath() {
		return Config::get('writing.admin-path-prefix').'/';
	}

	public static function isAvailableURI() {
		if(!in_array(Request::path(), Config::get('writing.protected_uris'))) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Returns true when the current URI belongs to the package or not.
	 */

	public static function isWritingURI() {

		$path = Request::path();
		$slug = $path;

		if($writing_path = Writing::path()) {

			// Config has path-prefix
			$URIContainsWritingPathPrefix = count(explode($writing_path, $path)) - 1;
			if($URIContainsWritingPathPrefix) {
				$slug = str_replace($writing_path, "", $path);
			} else {
				return false;
			}

			if($article = DB::table('articles')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}

		} else {

			// Config doesn't have path-prefix
			if($article = DB::table('articles')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

  public static function articlePropertyArray($article) {
    $properties = config('writing.properties');
    $article_properties = [];
    foreach($article->tags as $tag) {
      if(array_key_exists ( $tag->slug, $properties )) {
        $article_properties += $properties[$tag->slug];
      }
    }
    return $article_properties;
  }

  public static function articlePropertyFields($article) {
    foreach(Writing::articlePropertyArray($article) as $key=>$value) {
      $placeholder = $key;
      if(is_string($value)) {
        $placeholder = $value;
      } else if (is_array($value)) {
        if(array_key_exists('placeholder', $value)) {
          $placeholder = $value['placeholder'];
        }
      }
      $property = Property::where(['article_id' => $article->id, 'name' => $key])->first();
      $value = "";
      if($property && $property->value) {
        $value = $property->value;
      }
      echo '<p>'.Form::text($key, $value, array('placeholder' => $placeholder)).'</p>';
    }
  }

}
