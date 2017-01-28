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

	// Directory of Item Templates
	//'template-paths' => ['space::templates'],

	// View admin menu
	'view-admin-menu' => 'space::admin.c-menu',

	// URIs protected from Writing routes
	'protected_uris' => ['example', 'profile', 'about', 'magic'],

	// Special tags (add a class to articles containing them)
	'special-tags' => ['highlight'],

	// Middlewares to filter provided routes, e.g. 'auth'
	'middlewares' => ['web'],

	// Middlewares to filter provided routes, e.g. 'auth'
	'middlewares-admin' => ['login', 'web'],

	// Initial amount of articles to show in archive
	'published-show' => 5,

	// Initial amount of articles to show in archive
	'expected-show' => 100,

	// RSS Feed configuration
	'feed' =>
	[
		'route' => 'feed.xml',
		'title' => 'My Untitled Feed',
		'description' => 'A description here would come handy.',
		'show' => '30', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
		'default-image-src' => 'http://your-default.com/image.jpg'
	],

);
