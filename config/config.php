<?php

return [

	'path-prefix' => 'space', // (1) 'string' site.com/string/slug, (2) '' site.com/slug
	'admin-path-prefix' => 'admin',

	'view' => [
			'layout' => 'space::layout', 					// 'space::layout'
			'items' => 'space::base', 							// 'space::base'
		],

	'title' => 'Space',
	'title-short' => 'Spc',
	'description' => 'A simple web.',
	'css' => '/nonoesp/space/css/space.css', //
	'typekit' => '',

	'footer' => [
		'hide_credits' => false,
		'credits_text' => ''
		],

	//'template-paths' => ['space::templates'],
	//'special-tags' => ['highlight'],

	'header' => [
			'view' => 'space::partial.c-header',
			'classes' => ['white', 'absolute']
		],

	'media_links' => [
    'rss' => '/feed.xml',
    'facebook' => 'http://facebook.com/nonoesp',
    'twitter' => 'http://twitter.com/nonoesp',
    'instagram' => 'http://instagram.com/nonoesp',
    'dribbble' => 'http://dribbble.com/nonoesp',
    'github' => 'http://github.com/nonoesp',
    'star' => 'http://gettingsimple.com'
	],

	'protected_uris' => ['example', 'profile', 'about', 'magic'], // are not overriden by space

	'middlewares' => ['web'], // space routes
	'middlewares-admin' => ['login', 'web'], // space admin routes

	'published-show' => 5,
	'expected-show' => 100,

	'feed' => [
		'route' => 'feed', // (e.g. 'feed', or 'feed.xml')
		'title' => 'My Space Feed',
		'description' => 'A description here would come handy.',
		'show' => '30', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
		'default-image-src' => 'http://your-default.com/image.jpg',
		'default-author' => 'Nono Martínez Alonso'
	],

];
