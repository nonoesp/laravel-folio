<?php namespace Nonoesp\Writing;
 use Lang;

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
		return \HTML::link(\Config::get('writing.path').'/tag/'.$tag->slug, $tag->name, array('class' => $class));
	}

	public static function userURL($user) {
		return '/@'.$user->twitter;
	}

}