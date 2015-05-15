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
Route::group(array('prefix' => 'v1', 'middleware' => 'auth.api'), function () {
    // Campaigns.
    Route::group(array('middleware' => 'auth.token'), function () {
        Route::post('campaigns/{campaign_id}/signup', 'CampaignController@signup');
        Route::post('campaigns/{campaign_id}/reportback', 'CampaignController@reportback');
        Route::put('campaigns/{campaign_id}/reportback', 'CampaignController@updateReportback');
    });

    // Sessions.
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    // Users.
    Route::resource('users', 'UserController');
    Route::get('users/{term}/{id}/campaigns', 'CampaignController@show');
    Route::get('users/{term}/{id}', 'UserController@show');
    Route::post('users/{id}/avatar', 'AvatarController@store');

    // Api Keys.
    Route::resource('keys', 'KeyController');

});

