<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Routes for bookings.
 */
Route::get('bookings', 'App\Http\Controllers\BookingController@indexAll');

Route::middleware('auth:api')->group(function () {

    /**
     * Routes for booking records.
     */
    Route::get('bookings/{id}', 'App\Http\Controllers\BookingController@index');

    Route::get('booking/{id}', 'App\Http\Controllers\BookingController@show');

    Route::post('booking', 'App\Http\Controllers\BookingController@store');

    Route::put('booking/{id}', 'App\Http\Controllers\BookingController@update');

    Route::delete('booking/{id}', 'App\Http\Controllers\BookingController@destroy');

    Route::get('available/{date}', 'App\Http\Controllers\BookingController@listSlots');

    /**
     * Routes for meeting rooms.
     */
    Route::get('meeting_rooms', 'App\Http\Controllers\MeetingRoomController@index');
});


