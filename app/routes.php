<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

// https://api.dosomething.org/1/ 
Route::group(array('prefix' => '1'), function()
{	
	//Route::group(array('before' => 'auth.token'), function() {
		Route::post('campaigns/{id}/signup', 'CampaignController@signup');
	//}); 

	Route::post('login', 'UserController@login');
	Route::post('logout', 'UserController@logout');

	Route::get('/users/campaigns', 'CampaignController@index');
	Route::controller('/', 'UserController');  	 

});

