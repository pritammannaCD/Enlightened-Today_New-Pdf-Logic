<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

use App\Http\Middleware\Webhook;
use App\Http\Controllers\WebhookController;

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

Route::get('/', function () {
    return view('welcome');
});


// Route::any('/webhook', function (Request $request) {
//     return $request::all();
// })->middleware(Webhook::class);


Route::any('/webhook/{id}', [WebhookController::class, 'save_to_table'])->middleware(Webhook::class);
