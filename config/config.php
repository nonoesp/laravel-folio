<?php

return array(

	// Path of the blog, which can be
	// (1) a string like 'space' or 'journal' displays routes as yoursite.com/space/post-slug
	// (2) an empty string (i.e. '') displays routes as yoursite.com/post-slug
	'path-prefix' => 'space',

	// Admin path prefix, e.g. 'admin', 'space-admin', 'admin/space'
	'admin-path-prefix' => 'admin',

	// View of Main Template for base layout
	'template-view' => 'space::layout',

  // Title
	'title' => 'Space',

	// Title Cover
	'title-short' => 'Short Title',

	// Slogan, description
	'description' => 'A simple web.',

  // CSS stylesheet
	'css' => '', // defaults to /nonoesp/space/css/space.css

	// Typekit token (ignored if empty string, i.e. '')
	'typekit' => '',

	'footer-credits' => [
		'hidden' => false,
		'text' => ''
		],

	// Directory of Item Templates
	//'template-paths' => ['space::templates'],

	// Views
	'views' => [
			'home' => 'space::base', // defaults to 'space::base'
			'admin-menu' => 'space::admin.c-menu', // defaults to 'space::admin.c-menu'
		],

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

	// URIs protected from Writing routes
	'protected_uris' => ['example', 'profile', 'about', 'magic'],

	// Special tags (add a class to articles containing them)
	//'special-tags' => ['highlight'],

	// Middlewares to filter provided routes, e.g. 'auth'
	'middlewares' => [],

	// Middlewares to filter provided routes, e.g. 'auth'
	'middlewares-admin' => ['login'],

	// Initial amount of articles to show in archive
	'published-show' => 5,

	// Initial amount of articles to show in archive
	'expected-show' => 100,

	// RSS Feed configuration
	'feed' =>
	[
		'route' => 'feed', // (e.g. 'feed', or 'feed.xml')
		'title' => 'My Space Feed',
		'description' => 'A description here would come handy.',
		'show' => '30', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
		'default-image-src' => 'http://your-default.com/image.jpg',
		'default-author' => 'Nono Martínez Alonso'
	],

);
