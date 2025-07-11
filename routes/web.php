<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestTestController;
use App\Http\Controllers\Blog\Admin\PostController;
use App\Http\Controllers\DiggingDeeperController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::group(['prefix' => 'digging_deeper'], function () {
        Route::get('process-video', [DiggingDeeperController::class, 'processVideo'])
            ->name('digging_deeper.processVideo');
        Route::get('prepare-catalog', [DiggingDeeperController::class, 'prepareCatalog'])
            ->name('digging_deeper.prepareCatalog');
        Route::get('collections', [DiggingDeeperController::class, 'collections'])
            ->name('digging_deeper.collections');
    });

    Route::resource('rest', RestTestController::class)->names('restTest');
});

Route::prefix('admin/blog')->name('blog.admin.')->group(function () {
    Route::resource('posts', PostController::class)
        ->except(['show'])
        ->names('posts');
});

// Адмінка
$groupData = [
    'namespace' => 'App\Http\Controllers\Blog\Admin',
    'prefix' => 'admin/blog',
];
Route::group($groupData, function () {
    //BlogCategory
    $methods = ['index','edit','store','update','create',];
    Route::resource('categories', CategoryController::class)
        ->only($methods)
        ->names('blog.admin.categories');
    //BlogPost
    Route::resource('posts', PostController::class)
        ->except(['show'])                               //не робити маршрут для методу show
        ->names('blog.admin.posts');
});
