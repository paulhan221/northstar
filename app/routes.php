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
Route::group(array('prefix' => '1', 'before' => 'auth.api'), function()
{
  Route::group(array('before' => 'auth.token'), function() {
    Route::post('campaigns/{id}/signup', 'CampaignController@signup');
    Route::post('campaigns/{id}/reportback', 'CampaignController@reportback');
    Route::put('campaigns/{id}/reportback', 'CampaignController@updateReportback');
  });

  Route::post('login', 'UserController@login');
  Route::post('logout', 'UserController@logout');

  Route::resource('users', 'UserController');
  Route::get('users/campaigns/{term}/{id}', 'CampaignController@show');
  Route::get('users/{term}/{id}', 'UserController@show');

});

