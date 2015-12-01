<?php

return array(

	// Wrap all routes around a given path (i.e. writing/ or /blog)
	'use_path_prefix' => true,

	// Path without end/start slashes
	'path' => 'writing',

	// URIs protected from Writing routes
	'protected_uris' => ['example', 'profile', 'about', 'magic'],

	// Special tags (add a class to articles containing them)
	'special-tags' => ['case-study'],

	// Middlewares to filter provided routes
	'middlewares' => []//['login']
);