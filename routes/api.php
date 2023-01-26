<?php

use App\Http\Controllers\OzonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Libraries\Pyrus;
use App\Libraries\Parser;
use App\Process\Test;
use App\Http\Controllers\PyrusController;
use App\Http\Controllers\WebhookController;
use Illuminate\Routing\Route as RoutingRoute;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//POST
//Route::post('/auth', [Pyrus::class, 'auth']);
//Route::get('/tasks/{task_id}/comments', [PyrusController::class, 'addComment']);

//GET
/* Route::get('/tasks/{task_id}', [Pyrus::class, 'getTaskId']);
Route::get('/tasks/file/{task_id}', [PyrusController::class, 'getFile']);
Route::get('/parser', [Test::class, 'test']); */


Route::get('/mp/{task_id}', [WebhookController::class, 'getExcel']);
Route::get('/upload', [WebhookController::class, 'Upload']);
Route::get('/payfield/{task_id}', [WebhookController::class, 'getFrom']);


Route::get('/make/{task_id}', [WebhookController::class, 'getJson']);
Route::get('/tre/{task_id}', [WebhookController::class, 'tre']);
Route::get('/callback', [WebhookController::class, 'Callback']);
Route::post('/insalescallback', [WebhookController::class, 'CallbackInsales']);
Route::get('/payfield/{task_id}', [WebhookController::class, 'getFrom']);
Route::get('/pay/{task_id}', [WebhookController::class, 'Pay']);

Route::post('/webhook', [WebhookController::class, 'sendJson']);
Route::post('/getInsalesBonus', [WebhookController::class, 'getInsalesBonus']);

Route::get('/SetReworker/{task_id}', [WebhookController::class, 'SetReworker']);
Route::get('/test/{task_id}', [WebhookController::class, 'qr']);
Route::get('/comment/{task_id}', [WebhookController::class, 'Comment']);

Route::post('/InsalesBundle', [WebhookController::class, 'getInsales']);

Route::get('/ozon', [OzonController::class, 'getOzonDb']);
Route::get('/ozonfbo', [OzonController::class, 'OzonFbo']);
Route::get('/ozon/status/{post_number}', [OzonController::class, 'getStatus']);
