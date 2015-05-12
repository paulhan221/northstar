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

Route::get('/', function () {
  return Redirect::to('https://github.com/DoSomething/api');
});

// https://api.dosomething.org/v1/
Route::group(array('prefix' => 'v1', 'before' => 'auth.api'), function () {
  // Campaigns.
  Route::group(array('before' => 'auth.token'), function () {
    Route::post('campaigns/{id}/signup', 'CampaignController@signup');
    Route::post('campaigns/{id}/reportback', 'CampaignController@reportback');
    Route::put('campaigns/{id}/reportback', 'CampaignController@updateReportback');
  });

  // Sessions.
  Route::post('login', 'AuthController@login');
  Route::post('logout', 'AuthController@logout');

  // Users.
  Route::resource('users', 'UserController');
  Route::get('users/{term}/{id}/campaigns', 'CampaignController@show');
  Route::get('users/{term}/{id}', 'UserController@show');

  // Api Keys.
  Route::resource('keys', 'KeyController');
});

