<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('welcome');
});

Auth::routes(); //disable it to prevent default registration

//Route::get('register3123Gf37c', 'Auth\RegisterController@showRegistrationForm');
//Route::post('login', 'Auth\LoginController@login');
//Route::get('login', 'Auth\LoginController@showLoginForm');
//Route::post('logout', 'Auth\LoginController@logout');
//Route::post('register3123Gf37c', 'Auth\RegisterController@register');
//Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
//Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
//Route::post('password/reset', 'Auth\ResetPasswordController@reset');
//Route::post('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');


Route::group(['middleware' => ['auth']], function () {

    Route::get('/home', 'CallRecordController@index')->name('home');

    //campaigns
    Route::get('/campaigns', 'CampaignController@index')->name('campaigns');

    Route::post('/start-campaign', 'CampaignController@start');
    Route::post('/load/{campaign_id}', 'CampaignController@loadnumbers');   //must become obsolete
    Route::get('/load/{campaign_id}', function () {
        return view('load_numbers', ['title' => 'Load Numbers']);
    });

    //call records
    Route::get('/callrecords/{campaign_id}', 'CallRecordController@showCampaignCallRecords')->name('callrecords');
    Route::get('/callrecords-ini', 'CallRecordController@index')->name('callrecords-ini');
    Route::post('/callrecords', 'CallRecordController@show');

    //contacts
    Route::get('/contacts', 'ContactController@index')->name('contacts');
    Route::get('/load-contacts/', function () {
        return view('load_contacts', ['title' => 'Load Contacts']);
    });
    Route::post('/loadcontacts', 'ContactController@loadContacts');
//    Route::post('/api/js/contacts', 'ContactController@update');

});

Route::group(['middleware' => ['auth'], "prefix" => "/js/api/v2/"], function () {

    Route::put('contacts/edit', 'ContactController@update');
    Route::post('contacts/create', 'ContactController@store');
    Route::post('contacts/remove', 'ContactController@destroy');

    Route::post('create-campaign', 'CampaignController@createCampaignFromContacts');
    Route::post('campaigns/create', 'CampaignController@store');
    Route::post('campaigns/remove', 'CampaignController@destroy');

});
