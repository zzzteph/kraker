<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\Web\AgentsController;
use App\Http\Controllers\Web\HashlistsController;
use App\Http\Controllers\Web\HashtypesController;
use App\Http\Controllers\Web\TemplatesController;
use App\Http\Controllers\Web\TasksController;
use App\Http\Controllers\Web\Notifications\TelegramController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', function () {    return view('login');})->name('login');;
Route::post('login', [WebController::class, 'authentificate']);
Route::get('logout', [WebController::class, 'logout']);

Route::post('/notifications/telegram/callback', [TelegramController::class, 'callback']);


Route::middleware('auth')->group(function () {
	
	Route::get('/', function () {    return view('dashboard');});
	Route::get('/tasks',[TasksController::class, 'list']);

	Route::get('/tasks/new', [TasksController::class, 'new']);
	Route::get('/tasks/{id}', [TasksController::class, 'get']);
	Route::put('/tasks/{id}/start', [TasksController::class, 'start']);
	Route::put('/tasks/{id}/stop', [TasksController::class, 'stop']);
	
	Route::get('/inventory', [WebController::class, 'inventory']);
	Route::delete('/inventory/{id}', [WebController::class, 'deleteinventory']);
	Route::get('/agents', [AgentsController::class, 'list']);
	Route::get('/agents/{string}', [AgentsController::class, 'get']);
	Route::put('/agents/{string}/reset', [AgentsController::class, 'reset']);
	
	Route::delete('/agents/{string}', [AgentsController::class, 'delete']);
	Route::put('/agents/{string}', [AgentsController::class, 'update']);
	Route::get('/hashlists', [HashlistsController::class, 'list']);
	Route::get('/hashlists/new', [HashlistsController::class, 'new']);
	Route::post('/hashlists/new', [HashlistsController::class, 'create']);
	Route::get('/hashlists/{id}', [HashlistsController::class, 'get']);
	Route::delete('/hashlists/{id}', [HashlistsController::class, 'delete']);
	Route::get('/hashlists/{id}/source', [HashlistsController::class, 'source']);
	Route::get('/hashlists/{id}/cracked', [HashlistsController::class, 'cracked']);
	Route::get('/hashtypes/', [HashtypesController::class, 'list']);
	Route::put('/hashtypes/{id}', [HashtypesController::class, 'enable']);
	Route::get('/templates', [TemplatesController::class, 'list']);
	Route::get('/templates/new', [TemplatesController::class, 'new']);
	Route::post('/templates', [TemplatesController::class, 'create']);
	Route::get('/templates/{id}', [TemplatesController::class, 'get']);
	Route::delete('/templates/{id}', [TemplatesController::class, 'delete']);
	
	
	
	
	Route::get('/notifications/telegram', [TelegramController::class, 'get']);
	Route::post('/notifications/telegram', [TelegramController::class, 'post']);
	
	
});




