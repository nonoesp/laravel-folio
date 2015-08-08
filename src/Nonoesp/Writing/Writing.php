<?php namespace Nonoesp\Writing;
 
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

	public static function displayArticleTags($tags, $class) {
		$result = '';
		$idx = 0;
		foreach($tags as $tag) {
			$result .= Writing::articleTag($tag, $class);
			$idx++;
		}
		return $result;
	}

	public static function articleTag($tag, $class) {
		return \HTML::link(\Config::get('writing::path').'/tag/'.\Conner\Tagging\TaggingUtil::slug($tag), $tag, array('class' => $class));
	}

}