<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAgentEnabled;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'agents'], function () {

Route::get('/','App\Http\Controllers\Api\AgentsController@list' );
Route::post('/','App\Http\Controllers\Api\AgentsController@register' );
Route::post('/{string}','App\Http\Controllers\Api\AgentsController@checkRegistration' );
Route::get('/{string}','App\Http\Controllers\Api\AgentsController@get' );
Route::get('/{string}/info','App\Http\Controllers\Api\AgentsController@getInfo' );
Route::post('/{string}/info','App\Http\Controllers\Api\AgentsController@updateInfo');
Route::get('/{string}/inventory','App\Http\Controllers\Api\AgentsController@listInventory' );
Route::post('/{string}/inventory','App\Http\Controllers\Api\AgentsController@updateInventory' );
Route::post('/{string}/status','App\Http\Controllers\Api\AgentsController@heartbeat');
Route::get('/{string}/speedstats','App\Http\Controllers\Api\AgentsController@getSpeedStats');
Route::put('/{string}/speedstats','App\Http\Controllers\Api\AgentsController@updateSpeedStat');

});


Route::middleware([CheckAgentEnabled::class])->group(function () {
	
  Route::group(['prefix' => 'hashlists'], function () {
  Route::get('/','App\Http\Controllers\Api\HashlistsController@list' );
  Route::get('/{string}','App\Http\Controllers\Api\HashlistsController@get' );
  Route::get('/{string}/content','App\Http\Controllers\Api\HashlistsController@download' );
  Route::put('/{string}','App\Http\Controllers\Api\HashlistsController@updateInfo');
  
  });
  

  
  Route::group(['prefix' => 'templates'], function () {
  
  Route::get('/','App\Http\Controllers\Api\TemplatesController@list' );
  Route::get('/{string}','App\Http\Controllers\Api\TemplatesController@get' );
  Route::put('/{string}','App\Http\Controllers\Api\TemplatesController@updateInfo');
  });
  
  
  Route::get('/works','App\Http\Controllers\Api\WorksController@get' );
  Route::put('/jobs/{id}','App\Http\Controllers\Api\JobsController@put' );
  Route::get('/jobs/{id}','App\Http\Controllers\Api\JobsController@get' );

  
  
  Route::group(['prefix' => 'tasks'], function () {
  Route::get('/live','App\Http\Controllers\Api\TasksController@live' );
  Route::post('/','App\Http\Controllers\Api\TasksController@create' );
  Route::put('/{id}/priority','App\Http\Controllers\Api\TasksController@priority' );
  Route::put('/{id}','App\Http\Controllers\Api\TasksController@status' );
  
  
  Route::post('/calculate','App\Http\Controllers\Api\TasksController@calculate' );
	
	
  });
  
});



