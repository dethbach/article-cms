<?php

use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LoginRedirectController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/article', [PublicArticleController::class, 'index'])->name('public.article.index');
Route::get('/article/category/{category:slug}', [PublicArticleController::class, 'category'])->name('public.article.category');
Route::get('/article/{article:slug}', [PublicArticleController::class, 'show'])->name('public.article.show');


Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
Route::get('/robots.txt',  [SeoController::class, 'robots']);
Route::get('/llms.txt',    [SeoController::class, 'llms']);
Route::get('/feed.xml',    [SeoController::class, 'feed']);

// For Admin routes
Route::middleware(['role:admin', 'auth', 'account.status', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [LoginRedirectController::class, 'admin'])->name('admin.dashboard');

    Route::get('/admin/article/category', [ArticleCategoryController::class, 'index'])->name('admin.article.category');
    Route::get('/admin/article/archived-category', [ArticleCategoryController::class, 'show'])->name('admin.article.category.archive');
    Route::post('/admin/article/category/store', [ArticleCategoryController::class, 'store'])->name('admin.article.category.store');
    Route::post('/admin/article/category/update', [ArticleCategoryController::class, 'update'])->name('admin.article.category.update');
    Route::post('/admin/article/category/archieve', [ArticleCategoryController::class, 'archive'])->name('admin.article.category.status');
    Route::post('/admin/article/category/activate', [ArticleCategoryController::class, 'activate'])->name('admin.article.category.activate');
    Route::post('/admin/article/category/destroy', [ArticleCategoryController::class, 'destroy'])->name('admin.article.category.destroy');

    Route::get('/admin/article/thumbnail', [ThumbnailController::class, 'index'])->name('admin.article.thumbnail');
    Route::post('/admin/article/thumbnail/store', [ThumbnailController::class, 'store'])->name('admin.article.thumbnail.store');
    Route::post('/admin/article/thumbnail/update', [ThumbnailController::class, 'update'])->name('admin.article.thumbnail.update');

    Route::get('/admin/article/post', [ArticleController::class, 'index'])->name('admin.article.index');
    Route::get('/admin/article/write-post', [ArticleController::class, 'create'])->name('admin.article.create');
    Route::post('/admin/article/store-article', [ArticleController::class, 'store'])->name('admin.article.store');
    Route::post('/admin/article/delete', [ArticleController::class, 'destroy'])->name('admin.article.delete');
    Route::get('/admin/article/edit/{article:slug}', [ArticleController::class, 'edit'])->name('admin.article.edit');
    Route::put('/admin/article/update/{article:slug}', [ArticleController::class, 'update'])->name('admin.article.update');

    Route::get('/admin/profile', [ProfileUserController::class, 'index'])->name('admin.profile.index');
    Route::post('/admin/profile/photo', [ProfileUserController::class, 'updatePhoto'])->name('admin.profile.photo.update');
    Route::delete('/admin/profile/photo', [ProfileUserController::class, 'deletePhoto'])->name('admin.profile.photo.delete');
    Route::put('/admin/profile/information', [ProfileUserController::class, 'updateInformation'])->name('admin.profile.information.update');
    Route::put('/admin/profile/password', [ProfileUserController::class, 'updatePassword'])->name('admin.profile.password.update');
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings/users/update', [SettingController::class, 'updateUser'])->name('admin.settings.users.update');
});

// For User routes
Route::middleware(['role:user', 'auth', 'account.status', 'verified'])->group(function () {
    Route::get('/user/dashboard', [LoginRedirectController::class, 'user'])->name('user.dashboard');
});

require __DIR__.'/auth.php';
