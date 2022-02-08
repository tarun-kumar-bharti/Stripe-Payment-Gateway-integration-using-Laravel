<?php
 

Route::group(['middleware' => ['web', 'auth', 'role:USER']], function () {  
	Route::get('user-dashboard', array('as' => 'udashboard', 'uses' => 'UserController@showDashboard')); 	
	Route::get('get-planprice', array('as' => 'planprice', 'uses' => 'UserController@getPlanprice')); 	
	Route::get('/subscribe-physical-mailbox/{id?}', array('as' => 'subscribemail', 'uses' => 'UserController@subScribeMailbox'));
	Route::post('submit-subscription', ['as' => 'submitsubscription.post', 'middleware' => ['web'], 'uses' => 'UserController@submitSubscription']); 
	Route::get('/pay-for-physical-mailbox', ['as' => 'paysubscribemail', 'middleware' => ['web'], 'uses' => 'UserController@payMailbox']);
		 
	Route::post('payments', ['as' => 'payments.post', 'middleware' => ['web'], 'uses' => 'PaymentsController@paymentCheckout']); 
	Route::get('/payment-thank-you', ['as' => 'paymentthank', 'middleware' => ['web'], 'uses' => 'PaymentsController@paymentThank']);
	Route::get('/payment-cancel', ['as' => 'paymentcancel', 'middleware' => ['web'], 'uses' => 'PaymentsController@paymentCancel']);
	
	Route::get('/change-password', ['as' => 'changepass', 'middleware' => ['web'], 'uses' => 'UserController@changePassword']);
	Route::post('update-password', ['as' => 'updateauserpassword.post', 'middleware' => ['web'], 'uses' => 'Auth\PasswordController@savePasswordfromDashboard']); 
	 
	
});