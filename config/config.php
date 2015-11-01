<?php

return array(

	// Wrap all routes around a given path (i.e. writing/ or /blog)
	'use_path_prefix' => false,

	// Path without end/start slashes
	'path' => 'writing',

	// URIs protected from Writing routes
	'protected_uris' => ['example', 'profile', 'about', 'magic']
);