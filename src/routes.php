<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('pipeline::routing'), function() {
	Route::get('{path}', Config::get('pipeline::controller_action'))->where('path', '(?!\.\.)(.*)');
});