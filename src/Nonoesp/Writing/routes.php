<?php

Route::get('/writing/greeting', function() {
	return Writing::greeting();
});