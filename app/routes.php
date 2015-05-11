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
Route::group(array('prefix' => 'v1', 'before' => 'auth.api'), function()
{
  // Campaigns.
  Route::group(array('before' => 'auth.token'), function() {
    Route::post('campaigns/{id}/signup', 'Northstar\Http\Controllers\CampaignController@signup');
    Route::post('campaigns/{id}/reportback', 'Northstar\Http\Controllers\CampaignController@reportback');
    Route::put('campaigns/{id}/reportback', 'Northstar\Http\Controllers\CampaignController@updateReportback');
  });

  // Sessions.
  Route::post('login', 'Northstar\Http\Controllers\AuthController@login');
  Route::post('logout', 'Northstar\Http\Controllers\AuthController@logout');

  // Users.
  Route::resource('users', 'Northstar\Http\Controllers\UserController');
  Route::get('users/{term}/{id}/campaigns', 'Northstar\Http\Controllers\CampaignController@show');
  Route::get('users/{term}/{id}', 'Northstar\Http\Controllers\UserController@show');

  // Api Keys.
  Route::resource('keys', 'Northstar\Http\Controllers\KeyController');

});

