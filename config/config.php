<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site details
    |--------------------------------------------------------------------------
    */

    /*
     * The main site's title
     */
    'title' => 'Folio',

    /*
     * A short site title
     */ 
    'title-short' => 'Fol',

    /**
     * Brief site description
     * e.g. John's personal blog.
     */
    'description' => '',
        
    /**
     * Social preview
     * e.g. http://domain.com/og-image.jpg
     */
    'image' => '',
        
    /**
     * Author
     * e.g. John Doe
     */
    'author' => '',

    /*
    |--------------------------------------------------------------------------
    | Folio settings
    |--------------------------------------------------------------------------
    */

    /*
     * The base route for your Folio installation.
     * You can set Folio to run on the root '/' by setting this to '' or null.
     * (1) 'string' · site.com/string/slug
     * (2) ''       · site.com/slug
     */ 
    'path-prefix' => 'blog',

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
     * Translations that will show up when editing items.
     * e.g. ['en']
     */
    'translations' => ['en'],

    /*
     * Pattern of domains accepted by Folio.
     * (1) null - accept any domain
     * (2) pattern - just accept provided domains, e.g. 'localhost|example.com|127.0.0.1'
     */
    'domain-pattern' => env('FOLIO_DOMAIN_PATTERN', null),
        
    // Pattern of domains accepted by Folio (only to render items)
    // e.g. 'dev.domain.com|dev.domain.test'
    'domain-pattern-items' => env('FOLIO_DOMAIN_PATTERN_ITEMS', null),

    // Main domain to redirect items that are loaded from an unset accepted domain
    // If null - there's no domain redirection
    // If existent - redirection happens if no $item->domain() exists
    'main-domain' => env('FOLIO_DOMAIN', null),

    /**
     * Reserved routes.
     */
    'protected_uris' => [],

    /**
     * Middleware for Folio routes.
     */
    'middlewares' => ['web'],

    /**
     * Middleware for Folio admin routes.
     */
    'middlewares-admin' => ['web', 'auth'],

    /*
     * Prefix for database tables
     */ 
    'db-prefix' => 'folio_',

    /**
     * Sitemap path (e.g. '/sitemap.xml')
     * string or null
     * Can be generated with `php artisan folio:sitemap`
     */
    'sitemap' => null,

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    */

    /**
     * Folio assets directory path.
     */
    'assets-folder' => '/folio',

    /*
     * Path of your Folio stylesheets.
     * By default, this matches where Folio assets are deployed.
     */     
    'css' => '/folio/css/folio.css',

    /*
    * Settings for the file uploader.
    */          
    'uploader' => [
        'public-folder' => '/img/u',
        'disk' => 'public',
        'uploads-folder' => 'uploads',
        'allowed-file-types' => ['png', 'jpg', 'jpeg', 'gif', 'mp4'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Third-party services
    |--------------------------------------------------------------------------
    */

    /*
     * Your Google Analytics code. (Optional)
     * Folio won't track activity by logged admins.
     */             
    'google-analytics' => '',

    /**
     * Whether to use imgix.net as image CDN.
     */
    'imgix' => false,

    /*
     * Your typekit id. (Optional)
     */         
    'typekit' => '',

    /*
    |--------------------------------------------------------------------------
    | Subscribers
    |--------------------------------------------------------------------------
    |
    | Spatie\Newsletter requires MAILCHIMP_APIKEY & MAILCHIMP_LIST_ID in .env
    | to add subscribers to a Mailchimp list.
    |
    | Amazon SES needs to be configured to send email notifications.
    | 
    */

    'subscribers' => [
        // Add subscribers to a Mailchimp list?
        'add-to-newsletter' => env('FOLIO_SUBSCRIBER_ADD_TO_MAILCHIMP', false),     
        // Send "new subscriber" notifications to admins?
        'notify-admins' => env('FOLIO_SUBSCRIBER_NOTIFY_ADMINS', false),
        // Send "[SPAM] new subscriber" notifications to admins?
        'notify-admins-of-spam' => env('FOLIO_SUBSCRIBER_NOTIFY_ADMINS_OF_SPAM', false),
        // From email
        'from' => [
            'email' => env('MAIL_FROM_ADDRESS', 'no-reply@domain.com'),
            'name' => env('MAIL_FROM_NAME', 'Nono Martínez Alonso'),
        ],
        // To emails
        'to' => [
            'email' => ['your.email@domain.com'],
            'name' => ['Nono Martínez Alonso'],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    */

    'view' => [
        'layout' => 'folio::layout-v2',
        'item' => 'template.item',
        'collection' => 'template.item',
    ],

    /*
     * Path to search for custom templates.
     * eg. 'template' will search for templates on /resources/views/template.
     */
    'templates-path' => 'template',

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | Configure default views for Folio's template components.
    |
    | view      · view name                     · string
    | class     · main class                    · string
    | classes   · modifiers and extra classes   · array
    | hidden    · whether it's visible          · bool
    |
    */

    'html' => [
        'classes' => ['limit'],
    ],

    'cover' => [
        'view' => 'folio::partial.c-cover-v3',
        'hidden' => false,
        'class' => 'is-cool some-class',
        //'classes' => [''],
        'title' => '',
        'subtitles' => ['Laravel Folio', 'A Simple Site'],
        'footline' => 'Laravel Folio',
    ],

    'header' => [
        'view' => 'folio::partial.c-header-simple-v2',
        'hidden' => false,
        'class' => 'header-v1',
        'classes' => [],
        'title' => 'Folio',
        'svg' => null,
        'navigation' => [
            'blog' => '/blog',
            'about' => [
                'href' => '/about',
                'classes' => ['--external', 'u-opacity--half'],
            ],          
        ],
    ],  

    'footer' => [
        'view' => 'folio::partial.c-footer-v2',
        'hidden' => false,
        //'class' => '',
        'classes' => ['--left', '--border-top'],
    ],
        
    'credits' => [
        'view' => 'folio::partial.c-credits-v2',
        'hidden' => false,
        'classes' => '',
        'text' => '©',
    ],

    'subscribe' => [
        'view' => 'folio::partial.c-subscribe-v2',
        'hidden' => false,
        'class' => 'c-subscribe-v2',
        'classes' => ['--left'],
        'source' => 'folio_source',
        'medium' => 'folio_medium',
        'campaign' => 'folio_campaign',
        // 'text' => '{folio.subscribe-text}',
        'detail_text' => '',
        // 'button_text' => '',
    ],

    'menu' => [
        'view' => 'folio::partial.c-floating-menu',
        'hidden' => false,
        'items' => ['<i class="fa fa-gear"></i>' => '/admin'],
    ],  

    'admin-header' => [
        'view' => 'folio::partial.c-header',
        'classes' => ['white', 'absolute'],
        'data' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social media
    |--------------------------------------------------------------------------
    */

    'media_links' => [
        'rss' => '/feed.xml',
        'facebook' => 'http://facebook.com/nonoesp',
        'twitter' => 'http://twitter.com/nonoesp',
        'instagram' => 'http://instagram.com/nonoesp',
        'dribbble' => 'http://dribbble.com/nonoesp',
        'github' => 'http://github.com/nonoesp',
        'star' => 'http://gettingsimple.com',
    ],

    'social' => [
        'twitter' => [
            'handle' => '@nonoesp',
        ],
        'facebook' => [
            'app_id' => 'your-app-id',
            'author' => 'http://facebook.com/author-username',
            'publisher' => 'http://facebook.com/publisher-username',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Publishing
    |--------------------------------------------------------------------------
    */

    /*
     * Allowed Content-Type response types.
     */
    'allowed-content-types' => [
        'text/plain',
        'text/xml',
        'application/xml',
        'application/rss+xml',
        'image/svg+xml',
        'application/xhtml+xml',
        'application/json',
    ],

    /*
     * HTML tags to mark excerpts or publication teasers.
     */
    'more-tag' => '<!--more-->',
    'excerpt-tag' => '<!--excerpt-->',

    /*
     * Item property name to specify a redirection path that will
     * redirect to the item.
     */
    'item-redirection-property-name' => 'redirect',

    /*
     * The amount of published items to display in Folio's home page.
     * (The rest will be passed as a JavaScript JSON object for "load more".)
     * Use -1 to display all.
     */     
    'published-show' => -1,

    /*
     * The amount of items expected to display in Folio's home page.
     * Expected items are active items with a published date in the future.
     */ 
    'expected-show' => 100,

    /*
     * RSS feed configuration.
     */
    'feed' => [
		// 'view' => 'folio::template.rss',
        'route' => 'feed.xml', // (e.g. 'feed', or 'feed.xml')
        'title' => 'Folio Feed',
        'description' => 'Folio publications.',
        'show' => '30', // maximum amount of articles to display
        'logo' => '', // (optional) URL to your feed's logo
        'default-image-src' => 'http://your-default.com/image.jpg',
        'default-author' => 'Nono Martínez Alonso',
        'square-image-size' => 2048,
        'square-image-fit-method' => 'crop',
    ],
        
    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    'debug' => [
        'load-time' => true,
    ],

];