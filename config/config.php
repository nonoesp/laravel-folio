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
	 * The base route path for permanent links.
	 * @param string (for specifying a path) or empty (defaults to path-prefix)
	 * e.g., 'post' would create URLs like domain.com/post/245
	 */				
	'permalink-prefix' => '',

    /*
     * The location of the templates to use for the main layout of Folio, collections, and items
     */
	'view' => [
		'layout' => 'folio::layout', 				// defaults to 'folio::layout'
		'collection' => 'folio::template._base', 	// defaults to 'folio::template._base'
		'item' => 'folio::template._standard', 		// defaults to 'folio::template._standard'
	],

	/*
	 * Translations that will show up when editing items. Defaults to ['en'].
	 */
	'translations' => ['en'],

	/*
     * The path for Folio to search for custom templates.
	 * eg. 'template' with search for templates on /resources/views/template.
     */
	'templates-path' => 'template',

	/*
     * Pattern of domains accepted by Folio.
	 * (1) null - accept any domain
	 * (2) pattern - just accept provided domains, e.g. 'localhost|example.com|127.0.0.1'
     */
	'domain-pattern' => null,
	
	// Pattern of domains accepted by Folio (only to render items)
	// e.g. 'sketch.nono.ma|sketch.nono.localhost|expensed.me'
	'domain-pattern-items' => null,

	// Main domain to redirect items that are loaded from an unset accepted domain
	// If null - there's no domain redirection
	// If existent - redirection happens if no $item->domain() exists
	'main-domain' => env('FOLIO_DOMAIN', null),

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

	/*
	 * The main title of the Folio site.
	 */
	'title' => 'Folio',

	/*
	 * A short title
	 */	
	'title-short' => 'Fol',

	/*
	 * A brief description of your site.
	 * Default og_description.
	 */		
	'description' => 'A simple web.',

	/*
	 * A thumbnail for your site.
	 * Default og_image.
	 */			
	'image-src' => 'http://domain.com/img/image_src.jpg',

	/*
	 * Path of your Folio stylesheets.
	 * By default, this matches where Folio assets are deployed.
	 */		
	'css' => '/nonoesp/folio/css/folio.css',

	/*
	 * Your typekit id. (Optional)
	 */			
	'typekit' => '',

	/*
	 * Your Google Analytics code. (Optional)
	 * Folio won't track activity by logged admins.
	 */				
	'google-analytics' => '',

	/*
	 * The folder of a folder where media (images) are uploaded to.
	 * TODO: Include in uploader settings.
	 */				
	'media-upload-path' => '/img/u/',

	/*
	 * Settings for the media uploaded.
	 */			
	'uploader' => [
		'max_width' => 1500
	],

	'subscribers' => [
		/*
	 	* Wether Folio should send a "new subscriber" notification e-mail.
	 	*/				
		'should-notify' => false,
		/*
	 	* 'From' e-mail for notifications.
	 	*/				
		'from' => [
			'email' => 'from@domain.com',
			'name' => 'John Smith'
		],
		/*
	 	* 'To' e-mail for notifications.
	 	*/						
		'to' => [
			'email' => 'to@domain.com',
			'name' => 'Alissa Smith'
		]
	],

	/*
	* The configuration of Folio's template cover.
	*/					
	'cover' => [
		'hidden' => false,
		'title' => '',
		'subtitles' => ['Folio for Laravel','Making the web simple.'],
		'footline' => 'Folio for Laravel.',
		'class' => 'is-cool some-class'
	],

	/*
	* The configuration of Folio's template footer.
	*/			
	'footer' => [
		'hide_credits' => false,
		'credits_text' => ''
		],

	//'template-paths' => ['folio::templates'],
	//'special-tags' => ['highlight'],

	/*
	* The configuration of Folio's template header.
	*/			
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

	/*
	 * The HTML tags to use on Item's text field to specify where a
	 * exceprt or teaser end.
	 * 
	 */
	'more-tag' => '<!--more-->',
	'excerpt-tag' => '<!--excerpt-->',

	/*
	 * Item property name to specify a redirection path that will
	 * redirect to the item.
	 * 
	 */
	'item-redirection-property-name' => 'redirect',

	/*
	 * The amount of published items to display in Folio's home page.
	 * (The rest will be passed as a JavaScript JSON object for "load more".)
	 */		
	'published-show' => 5,

	/*
	 * The amount of items expected to display in Folio's home page.
	 * Expected items are active items with a published date in the future.
	 */	
	'expected-show' => 100,

	/*
	 * The RSS Feed configuration.
	 */
	'feed' => [
		'route' => 'feed.xml', // (e.g. 'feed', or 'feed.xml')
		'title' => 'Folio Feed',
		'description' => 'Folio publications.',
		'show' => '30', // maximum amount of articles to display
		'logo' => '', // (optional) URL to your feed's logo
		'default-image-src' => 'http://your-default.com/image.jpg',
		'default-author' => 'Nono Martínez Alonso',
	],

	'social' => [
		'twitter' => [
			'handle' => '@nonoesp',
		],
		'facebook' => [
			'app_id' => 'your-app-id',
			'author' => 'http://facebook.com/author-username',
			'publisher' => 'http://facebook.com/publisher-username',
        ],
    ],

    'debug' => [
        'load-time' => false,
    ],

];