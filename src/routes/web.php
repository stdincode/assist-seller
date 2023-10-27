<?php

use App\Http\Controllers\TelegramBotController;
use App\Http\Middleware\Cors;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

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

Route::get('/storage/experts/avatars/{filename}', function ($filename) {
    $path = storage_path('app/public/experts/avatars/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->middleware(Cors::class);

Route::get('/storage/experts/video/{filename}', function ($filename) {
    $path = storage_path('app/public/experts/video/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->middleware(Cors::class);

Route::any('webhook', [TelegramBotController::class, 'handleWebhook']);

Route::get('set_webhook', [TelegramBotController::class, 'setWebhook']);

