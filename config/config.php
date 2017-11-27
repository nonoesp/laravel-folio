<?php

return [

    /*
     * The base route for your Folio installation.
	 * You can set Folio to run on the root '/' by setting this to '' or null.
	 * (1) 'string' site.com/string/slug
	 * (2) '' site.com/slug
     */	
	'path-prefix' => 'folio',

    /*
     * The base route for your the admin of your Folio installation.
     */		
	'admin-path-prefix' => 'admin',

    /*
     * The location of the templates to use for the main layout of Folio, collections, and items
     */
	'view' => [
		'layout' => 'folio::layout', 				// defaults to 'folio::layout'
		'collection' => 'folio::template._base', 	// defaults to 'folio::template._base'
		'item' => 'folio::template._standard', 		// defaults to 'folio::template._standard'
	],

	/*
     * The path for Folio to search for custom templates.
	 * eg. 'template' with search for templates on /resources/views/template.
     */
	'templates-path' => 'template',

	/*
     * Pattern of domains accepted by Folio.
	 * e.g. 'localhost|example.com|127.0.0.1'
     */
	'domain-pattern' => 'yourdomain.com|localhost|127.0.0.1', 
	
	/*
	 * Prefix for database tables
     */	
	'db-prefix' => 'folio_',

	/*
     * Wether subscribers should be added to Mailchimp or not.
	 * Mailchimp configuration should be added to .env for Spatie\Newsletter as follows:
	 * MAILCHIMP_APIKEY=your-api-key
	 * MAILCHIMP_LIST_ID=your-list-id
     */	
	'should-add-to-mailchimp' => false,

	'title' => 'Folio',
	'title-short' => 'Spc',
	'description' => 'A simple web.',
	'image_src' => 'http://domain.com/img/image_src.jpg',

	'css' => '/nonoesp/folio/css/folio.css',

	'typekit' => '',

	'google-analytics' => '',

	// path where media is uploaded
	'media-upload-path' => '/img/u/',

	'uploader' => [
		'max_width' => 1500
	],

	'subscribers' => [
		'should-notify' => false,
		'from' => [
			'email' => 'from@domain.com',
			'name' => 'John Smith'
		],
		'to' => [
			'email' => 'to@domain.com',
			'name' => 'Alissa Smith'
		]
	],

	'cover' => [
		'title' => '',
		'subtitles' => ['Folio for Laravel','Making the web simple.'],
		'footline' => 'Folio for Laravel.'
	],

	'footer' => [
		'hide_credits' => false,
		'credits_text' => ''
		],

	//'template-paths' => ['folio::templates'],
	//'special-tags' => ['highlight'],

	'header' => [
			'title' => 'Folio',		
			'view' => 'folio::partial.c-header',
			'classes' => ['white', 'absolute'],
			'data' => []
		],

	'admin-header' => [
			'view' => 'folio::partial.c-header',
			'classes' => ['white', 'absolute'],
			'data' => []
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

	'protected_uris' => ['example', 'profile', 'about', 'magic'], // are not overriden by folio

	'middlewares' => ['web'], // folio routes
	'middlewares-admin' => ['login', 'web'], // folio admin routes

	'published-show' => 5,
	'expected-show' => 100,

	'feed' => [
		'route' => 'feed.xml', // (e.g. 'feed', or 'feed.xml')
		'title' => 'Folio Feed',
		'description' => 'Folio publications.',
		'show' => '30', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
		'default-image-src' => 'http://your-default.com/image.jpg',
		'default-author' => 'Nono Martínez Alonso'
	],

	'social' => [
		'twitter' => [
			'handle' => '@nonoesp'
		],
		'facebook' => [
			'app_id' => 'your-app-id',
			'author' => 'http://facebook.com/author-username',
			'publisher' => 'http://facebook.com/publisher-username',
		]
	]

];