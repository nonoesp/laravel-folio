<?php namespace Nonoesp\Writing;
 
class Writing {
 
	public static function greeting() {

		echo 'path: '.\Config::get('writing::path');
		echo '<br><br>';
		echo 'Hello, I am the Writing class. An update here.';
	}

}