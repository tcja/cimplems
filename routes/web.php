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

Route::middleware([App\Http\Middleware\UserCheck::class])->group(function () {
    // Pages routes
    Route::post('/delete_page', [App\Http\Controllers\PageController::class, 'deletePage']);
    Route::post('/edit_page', [App\Http\Controllers\PageController::class, 'editPage']);
    Route::post('/add_page', [App\Http\Controllers\PageController::class, 'addPage']);
    Route::post('/show_page_ajax', [App\Http\Controllers\PageController::class, 'showPageAjax'])->withoutMiddleware([App\Http\Middleware\UserCheck::class]);;
    Route::post('/change_menu_order', [App\Http\Controllers\PageController::class, 'changeMenuOrder']);
    Route::post('/change_page_name', [App\Http\Controllers\PageController::class, 'changePageName']);
    Route::post('/change_page_state', [App\Http\Controllers\PageController::class, 'changePageState']);
    Route::post('/upload_image', [App\Http\Controllers\PageController::class, 'uploadImage']);
    Route::post('/delete_site_image', [App\Http\Controllers\PageController::class, 'deleteSiteImage']);
    Route::get('/list_private_pages', [App\Http\Controllers\PageController::class, 'listPrivatePages']);
    Route::get('/show_home_page', [App\Http\Controllers\PageController::class, 'showHomePage']);

    Route::get('/{slug}', [App\Http\Controllers\PageController::class, 'index'])->where('slug', '^(?!list_private_pages|show_home_page|get_user_email|a\/logout).*$')->withoutMiddleware([App\Http\Middleware\UserCheck::class]); // Main route for handling a page

    // Gallery routes
    Route::post('/do_upload', [App\Http\Controllers\GalleryController::class, 'doUpload']);
    Route::post('/delete_image', [App\Http\Controllers\GalleryController::class, 'deleteImage']);
    Route::post('/delete_gallery', [App\Http\Controllers\GalleryController::class, 'deleteGallery']);
    Route::post('/create_gallery', [App\Http\Controllers\GalleryController::class, 'createGallery']);
    Route::post('/edit_galleries', [App\Http\Controllers\GalleryController::class, 'editGalleries']);
    Route::post('/edit_image_show_form', [App\Http\Controllers\GalleryController::class, 'editImageShowForm']);
    Route::post('/edit_image', [App\Http\Controllers\GalleryController::class, 'editImage']);
    Route::post('/change_gal_page', [App\Http\Controllers\GalleryController::class, 'changeGalleryPage'])->withoutMiddleware([App\Http\Middleware\UserCheck::class]);

    // Contact routes
    Route::post('/contact', [App\Http\Controllers\ContactController::class, 'sendForm'])->withoutMiddleware([App\Http\Middleware\UserCheck::class]);;

    // Auth routes
    Route::post('/a/login', [App\Http\Controllers\Auth\LoginController::class, '__construct'])->withoutMiddleware([App\Http\Middleware\UserCheck::class])->middleware('browsercheck');
    Route::get('/a/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

    // User routes
    Route::post('/change_user_email', [App\Http\Controllers\UserController::class, 'changeUserEmail']);
    Route::post('/change_user_password', [App\Http\Controllers\UserController::class, 'changeUserPassword']);
    Route::post('/check_user_password', [App\Http\Controllers\UserController::class, 'checkUserPassword']);
    Route::get('/get_user_email', [App\Http\Controllers\UserController::class, 'getUserEmail']);
});
