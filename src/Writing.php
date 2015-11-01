<?php namespace Nonoesp\Writing;
 
 use Lang;
 use Config;
 use Request;
 use HTML;

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

}