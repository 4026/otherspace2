<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Laravel standard auth routes
Route::auth();

//Homepage
Route::get('/', 'HomeController@index');

//AJAX calls
Route::get('/location', 'LocationController@getLocation');
Route::post('/message', 'LocationController@addMessage');