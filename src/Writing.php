<?php namespace Nonoesp\Writing;
 
 use Lang;
 use Config;
 use Request;
 use HTML;
 use DB;
 use Article; // Must be defined in your aliases

class Writing {

	public static function articleCategoryClass($tags, $class) {

		$categories = \Config::get('blog.special-tags'); //TODO: lang in package

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
		return HTML::link(Writing::path().'tag/'.$tag->slug, $tag->name, array('class' => $class));
	}

	public static function userURL($user) {
		return '/@'.$user->twitter;
	}

	public static function path() {
		if(Config::get('writing.use_path_prefix')) {
			return Config::get('writing.path').'/';
		} else {
			return '';
		}
	}

	public static function isAvailableURI() {
		if(!in_array(Request::path(), Config::get('writing.protected_uris'))) {
			return true;
		} else {
			return false;
		}
	}

	public static function isWritingURI() {


		$path = Request::path();
		$writing_path = Writing::path();
		$URIhasWritingPathPrefix = 0;
		$isUsingPathPrefix = Config::get('writing.use_path_prefix');
		$slug = $path;

		/*if($isUsingPathPrefix) {
			echo 'using_prefix = true;<br>';
		} else {
			echo 'using_prefix = false;<br>';
		}*/

		if($isUsingPathPrefix == true) {
			$URIhasWritingPathPrefix = count(explode($writing_path, $path)) - 1;
			if($URIhasWritingPathPrefix) {
				$slug = str_replace($writing_path, "", $path);
			} else {
				return false;
			}

			if($article = DB::table('articles')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}						
		}

		if(!$isUsingPathPrefix) {

			if($URIhasWritingPathPrefix) {
				return false;
			}

			if($article = DB::table('articles')->whereSlug($slug)->first()) {
				return true;
			} else {
				return false;
			}		
		}

		return false;
	}

}