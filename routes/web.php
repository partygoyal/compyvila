<?php
use App\Http\Controllers\{EnquiryController, PublicationController, SubscriptionController};
use App\Http\Controllers\Admin\{ArticleController, AuthController, DashboardController, EventController, RecordController, SettingsController, SnapshotController, SubscriberController};
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicationController::class, 'home'])->name('home');
Route::get('/read/{slug}', [PublicationController::class, 'article'])->name('articles.show');
Route::get('/calendar', [PublicationController::class, 'calendar'])->name('calendar');
Route::get('/media-kit/download', [PublicationController::class, 'downloadKit'])->name('media.download');
Route::get('/sitemap.xml', [PublicationController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PublicationController::class, 'robots'])->name('robots');
Route::get('/{section}', [PublicationController::class, 'listing'])
    ->whereIn('section', ['news', 'analysis', 'bitcoin', 'ethereum', 'altcoins', 'learn', 'search', 'watchlist'])->name('section');
Route::get('/{page}', [PublicationController::class, 'page'])
    ->whereIn('page', ['media-kit', 'market-notes', 'contact', 'editorial-policy', 'privacy', 'terms'])->name('pages.show');
Route::post('/subscribe', [SubscriptionController::class, 'store'])->middleware('throttle:20,1')->name('subscribe');
Route::post('/media-enquiry', [EnquiryController::class, 'store'])->middleware('throttle:10,1')->name('media.enquiry');

Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'store'])->name('admin.login.store');
Route::prefix('admin')->name('admin.')->middleware('editorial')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/article', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::post('/article', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/event', [EventController::class, 'edit'])->name('events.edit');
    Route::post('/event', [EventController::class, 'store'])->name('events.store');
    Route::get('/snapshot', [SnapshotController::class, 'edit'])->name('snapshots.edit');
    Route::post('/snapshot', [SnapshotController::class, 'store'])->name('snapshots.store');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/password', [SettingsController::class, 'password'])->name('password.update');
    Route::get('/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::delete('/records/{type}/{id}', [RecordController::class, 'destroy'])->whereNumber('id')->name('records.destroy');
});
