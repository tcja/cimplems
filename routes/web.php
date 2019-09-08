<?php

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

// Default abort page for forms
Route::post('/abort', function () {
    abort(404);
});

// Pages routes
Route::post('/delete_page', 'PageController@deletePage')->middleware('usercheck');
Route::post('/edit_page', 'PageController@editPage')->middleware('usercheck');
Route::post('/add_page', 'PageController@addPage')->middleware('usercheck');
Route::post('/show_page_ajax', 'PageController@showPageAjax');
Route::post('/change_menu_order', 'PageController@changeMenuOrder')->middleware('usercheck');
Route::post('/change_page_name', 'PageController@changePageName')->middleware('usercheck');
Route::post('/change_page_state', 'PageController@changePageState')->middleware('usercheck');
Route::post('/upload_image', 'PageController@uploadImage')->middleware('usercheck');
Route::post('/delete_site_image', 'PageController@deleteSiteImage')->middleware('usercheck');
Route::get('/list_private_pages', 'PageController@listPrivatePages')->middleware('usercheck');
Route::get('/show_home_page', 'PageController@showHomePage')->middleware('usercheck');
Route::get('/{slug}', 'PageController@index')->where('slug', '^(?!admin|login|logout|list_private_pages|show_home_page|get_user_email).*$'); // Main route for handling a page

// Gallery routes
Route::post('/do_upload', 'GalleryController@doUpload')->middleware('usercheck');
Route::post('/delete_image', 'GalleryController@deleteImage')->middleware('usercheck');
Route::post('/delete_gallery', 'GalleryController@deleteGallery')->middleware('usercheck');
Route::post('/create_gallery', 'GalleryController@createGallery')->middleware('usercheck');
Route::post('/edit_galleries', 'GalleryController@editGalleries')->middleware('usercheck');
Route::post('/edit_image_show_form', 'GalleryController@editImageShowForm')->middleware('usercheck');
Route::post('/edit_image', 'GalleryController@editImage')->middleware('usercheck');
Route::post('/change_gal_page', 'GalleryController@changeGalleryPage');

// Contact routes
Route::post('/contact', 'ContactController@sendForm');

// Auth routes
Route::post('/login', 'Auth\LoginController@__construct')->middleware('browsercheck');
Route::get('/login', 'Auth\LoginController@__construct')->middleware('browsercheck');
Route::get('/logout', 'Auth\LoginController@logout')->middleware('usercheck');

// User routes
Route::post('/change_user_email', 'UserController@changeUserEmail')->middleware('usercheck');
Route::post('/change_user_password', 'UserController@changeUserPassword')->middleware('usercheck');
Route::post('/check_user_password', 'UserController@checkUserPassword')->middleware('usercheck');
Route::get('/get_user_email', 'UserController@getUserEmail')->middleware('usercheck');
