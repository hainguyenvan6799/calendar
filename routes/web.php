<?php

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | This file is where you may define all of the routes that are handled
 * | by your application. Just tell Laravel the URIs it should respond
 * | to using a Closure or controller method. Build something great!
 * |
 */
use Illuminate\Http\Request;

Route::get ( '/', function () {
	return view ( 'welcome' );
} );
Route::post ( 'test', function (Request $request) {
	\Stripe\Stripe::setApiKey ( 'sk_test_KEGrVZIG4Ea4SJ9O6N1jzIhd00keMDnAz1' );
	try {
		\Stripe\Charge::create ( array (
				"amount" => 300 * 100,
				"currency" => "usd",
				"source" => $request->input ( 'stripeToken' ), // obtained with Stripe.js
				"description" => "Test payment." 
		) );
		Session::flash ( 'success-message', 'Payment done successfully !' );
		return Redirect::back ();
	} catch ( \Exception $e ) {
		Session::flash ( 'fail-message', "Error! Please Try again." );
		return Redirect::back ();
	}
} );

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('verify', 'VerifyController@getVerify');
Route::post('verify', 'VerifyController@postVerify');
// Route::get('calendar/{month}/{year}', 'CalendarController@showCalendar');

// Route::get('previousMonth', 'CalendarController@getPreviousMonth');
// Route::get('currentMonth', 'CalendarController@getCurrentMonth');
// Route::get('nextMonth', 'CalendarController@getNextMonth');

Route::get('test1', 'CalendarController@getTest');

Route::get('calendar', 'CalendarController@getCalendar');
//<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

Route::post('formBooking/{dateBooking}', 'CalendarController@postFormBooking');

Route::get('timeslot/{dateBooking}', 'CalendarController@getTimeSlot');

