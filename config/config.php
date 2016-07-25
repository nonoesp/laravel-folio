<?php

return array(

	// Path of the blog, which can be
	// (1) a string like 'writing' or 'journal' displays routes as yoursite.com/writing/post-slug
	// (2) an empty string (i.e. '') displays routes as yoursite.com/post-slug
	'path-prefix' => 'writing',

	// Admin path prefix, e.g. 'admin', 'writing-admin', 'admin/writing'
	'admin-path-prefix' => 'admin',

	// Template View for base layout
	'template-view' => 'writing::layout',

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
		'show' => '2', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
	],

	// Experimental
	'layers' => 
	[
		'photos' =>
		[
			'path' => 'photos',
			'tags' => ['photos'],
			'view' => 'writing::layer.photos'
		],
		'sketches' =>
		[
			'path' => 'sketches',
			'tags' => ['make'],
			'view' => 'writing::layer.sketches'
		],
		'notes' =>
		[
			'path' => 'notes',
			'tags' => ['simplify', 'highlight'],
			'view' => 'writing::layer.notes'
		]
	]
);




