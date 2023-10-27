<?php

use App\Http\Controllers\Api\Client\SchemaController;
use App\Http\Controllers\Api\Client\V1\AuthController;
use App\Http\Controllers\Api\Client\V1\DictionaryController;
use App\Http\Controllers\Api\Client\V1\ExpertController;
use App\Http\Controllers\Api\Client\V1\StudentController;
use App\Http\Controllers\Api\Client\V1\TelegramController;
use App\Http\Middleware\ContentsValidators\Auth\ValidateAuthRequest;
use App\Http\Middleware\ContentsValidators\Expert\ValidateCreateExpertRequest;
use App\Http\Middleware\ContentsValidators\Expert\ValidateUpdateExpertPaymentRequest;
use App\Http\Middleware\ContentsValidators\Expert\ValidateUpdateExpertRequest;
use App\Http\Middleware\ContentsValidators\Place\ValidateCreatePlaceRequest;
use App\Http\Middleware\ContentsValidators\Place\ValidateUpdatePlaceRequest;
use App\Http\Middleware\ContentsValidators\Specialization\ValidateCreateSpecializationRequest;
use App\Http\Middleware\ContentsValidators\Specialization\ValidateUpdateSpecializationRequest;
use App\Http\Middleware\ContentsValidators\Student\ValidateCreateStudentRequest;
use App\Http\Middleware\ContentsValidators\Student\ValidateUpdateStudentRequest;
use App\Http\Middleware\ContentsValidators\TelegramClient\ValidateCreateTelegramClientRequest;
use App\Http\Middleware\LogRequest;
use App\Http\Middleware\ValidateResourceIdAsInteger;
use Illuminate\Support\Facades\Route;

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

Route::get('/schema/{type}', [SchemaController::class, 'schema'])->where('type', 'json|yaml');

Route::group([
    'prefix' => 'v1',
    'middleware' => [
        LogRequest::class,
    ],
], function () {
    Route::group([
        'controller' => AuthController::class,
        'middleware' => 'api',
        'prefix' => 'auth',
    ], function () {
        Route::post('login', 'login')->name('login')->middleware(ValidateAuthRequest::class);
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('me', 'me');
    });

    Route::group([
        'controller' => DictionaryController::class,
        'middleware' => 'jwt.auth',
    ], function () {
        Route::get('places/', 'placesIndex');
        Route::post('places/', 'placeCreate')->middleware(ValidateCreatePlaceRequest::class);
        Route::post('places/{id}', 'placeUpdate')->middleware([
            ValidateResourceIdAsInteger::class,
            ValidateUpdatePlaceRequest::class,
        ]);
        Route::delete('places/{id}', 'placeDelete')->middleware(ValidateResourceIdAsInteger::class);
    });

    Route::group([
        'controller' => DictionaryController::class,
        'middleware' => 'jwt.auth',
    ], function () {
        Route::get('specializations/', 'specializationsIndex');
        Route::post('specializations/', 'specializationCreate')->middleware(ValidateCreateSpecializationRequest::class);
        Route::post('specializations/{id}', 'specializationUpdate')->middleware([
            ValidateResourceIdAsInteger::class,
            ValidateUpdateSpecializationRequest::class,
        ]);
        Route::delete('specializations/{id}', 'specializationDelete')->middleware(ValidateResourceIdAsInteger::class);
    });

    Route::group([
        'controller' => ExpertController::class,
        'middleware' => 'jwt.auth',
    ], function () {
        Route::get('experts/', 'expertsIndex');
        Route::get('experts/{id}', 'expertShow')->middleware(ValidateResourceIdAsInteger::class);
        Route::post('experts/', 'expertCreate')->middleware(ValidateCreateExpertRequest::class);
        Route::post('experts/{id}', 'expertUpdate')->middleware([
            ValidateResourceIdAsInteger::class,
            ValidateUpdateExpertRequest::class,
        ]);
        Route::delete('experts/{id}', 'expertDelete')->middleware(ValidateResourceIdAsInteger::class);

        Route::get('experts/{id}/payments', 'expertPaymentsIndex')->middleware(ValidateResourceIdAsInteger::class);
        Route::post('experts/{id}/payments', 'expertPaymentCreate')->middleware([
            ValidateResourceIdAsInteger::class,
        ]);
        Route::post('experts/{id}/payments/{payment_id}', 'expertPaymentUpdate')->middleware([
            ValidateResourceIdAsInteger::class,
            ValidateUpdateExpertPaymentRequest::class,
        ]);

        Route::get('expert_payment_statuses', 'expertPaymentStatusesIndex');

    });

    Route::group([
        'controller' => StudentController::class,
        'middleware' => 'jwt.auth',
    ], function () {
        Route::get('students/', 'studentsIndex');
        Route::get('students/{id}', 'studentShow')->middleware(ValidateResourceIdAsInteger::class);
        Route::post('students/', 'studentCreate')->middleware(ValidateCreateStudentRequest::class);
        Route::post('students/{id}', 'studentUpdate')->middleware([
            ValidateResourceIdAsInteger::class,
            ValidateUpdateStudentRequest::class,
        ]);
        Route::delete('students/{id}', 'studentDelete')->middleware(ValidateResourceIdAsInteger::class);

    });


});
