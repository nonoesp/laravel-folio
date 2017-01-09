<?php

return array(

	// Path of the blog, which can be
	// (1) a string like 'writing' or 'journal' displays routes as yoursite.com/writing/post-slug
	// (2) an empty string (i.e. '') displays routes as yoursite.com/post-slug
	'path-prefix' => 'writing',

	// Admin path prefix, e.g. 'admin', 'writing-admin', 'admin/writing'
	'admin-path-prefix' => 'admin',

	// View Template for base layout
	'template-view' => 'writing::layout',

	// View admin menu
	'view-admin-menu' => 'writing::admin.c-menu',

	// URIs protected from Writing routes
	'protected_uris' => ['example', 'profile', 'about', 'magic'],

	// Special tags (add a class to articles containing them)
	// 'special-tags' => ['highlight'],

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

	// Experimental
	'properties' =>
	[
		'project' =>
		[
			'architect' => 'Architect (e.g. FJMT Studio)',
			'contractor' => 'Contractor (e.g. Mirvac)',
			'client' => 'Client (e.g. Empire Glass and Aluminium)',
			'completion_date' => ['type' => 'varchar', 'placeholder' => 'Date of Completion (e.g. August, 2016)'],
			'location' => 'Location (e.g. 200 George Street, Sydney)',
			'fabrication' => 'Fabrication (e.g. Terry Tisdale, OX Engineering)',
			'structure' => 'Structure (e.g. ARUP)',
			'lighting' => 'Lighting (e.g. ARUP)',
			'photo_credits' => 'Photo Credits (e.g. Brett Boardman)'
			],
		'case-study' =>
			[
				'technology' => 'Technology',
				'price' => 'Price'
			]
	],

	// Experimental
	// 'layers' =>
	// [
	// 	'photos' =>
	// 	[
	// 		'path' => 'photos',
	// 		'tags' => ['photos'],
	// 		'title'=> 'Photos',
	// 		'view' => 'writing::layer.photos'
	// 	],
	// 	'sketches' =>
	// 	[
	// 		'path' => 'sketches',
	// 		'tags' => ['make'],
	// 		'title'=> 'Sketches',
	// 		'view' => 'writing::layer.sketches'
	// 	],
	// 	'notes' =>
	// 	[
	// 		'path' => 'notes',
	// 		'tags' => ['simplify', 'highlight'],
	// 		'title'=> 'Notes',
	// 		'view' => 'writing::layer.notes'
	// 	]
	// ]
);
