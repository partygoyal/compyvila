<?php
use App\Http\Controllers\{EnquiryController, PublicationController, SubscriptionController};
use App\Http\Controllers\Admin\{ArticleController, AuthController, DashboardController, EventController, RecordController, SettingsController, SnapshotController, SubscriberController};
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicationController::class, 'home'])->name('home');
Route::get('/read/{slug}', [PublicationController::class, 'article'])->name('articles.show');
Route::get('/creator-network', [\App\Http\Controllers\CreatorController::class, 'index'])->name('creators.index');
Route::post('/creator-network', [\App\Http\Controllers\CreatorController::class, 'store'])->middleware('throttle:5,1')->name('creators.store');
Route::get('/quiz', [\App\Http\Controllers\ToolController::class, 'quiz'])->name('quiz');
Route::redirect('/tools/crypto-knowledge-quiz', '/quiz', 301);
Route::get('/tools', [\App\Http\Controllers\ToolController::class, 'index'])->name('tools.index');
Route::get('/tools/{slug}', [\App\Http\Controllers\ToolController::class, 'show'])->name('tools.show');
Route::get('/funding', [\App\Http\Controllers\FundingController::class, 'index'])->name('funding.index');
Route::post('/funding/submit', [\App\Http\Controllers\FundingController::class, 'submit'])->middleware('throttle:5,1')->name('funding.submit');
Route::get('/funding/{slug}', [\App\Http\Controllers\FundingController::class, 'show'])->name('funding.show');
Route::get('/calendar', [PublicationController::class, 'calendar'])->name('calendar');
Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
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
    Route::get('/funding', [\App\Http\Controllers\Admin\FundingController::class, 'index'])->name('funding.index');
    Route::get('/funding/round', [\App\Http\Controllers\Admin\FundingController::class, 'edit'])->name('funding.edit');
    Route::post('/funding/round', [\App\Http\Controllers\Admin\FundingController::class, 'save'])->name('funding.save');
    Route::delete('/funding/round/{round}', [\App\Http\Controllers\Admin\FundingController::class, 'destroy'])->whereNumber('round')->name('funding.delete');
    Route::get('/funding/startup', [\App\Http\Controllers\Admin\FundingController::class, 'startupEdit'])->name('funding.startup.edit');
    Route::post('/funding/startup', [\App\Http\Controllers\Admin\FundingController::class, 'startupSave'])->name('funding.startup.save');
    Route::match(['get','patch'], '/funding/submissions/{submission}', [\App\Http\Controllers\Admin\FundingController::class, 'submission'])->whereNumber('submission')->name('funding.submission');
    Route::delete('/funding/submissions/{submission}', [\App\Http\Controllers\Admin\FundingController::class, 'deleteSubmission'])->whereNumber('submission')->name('funding.submission.delete');
    Route::get('/creators', [\App\Http\Controllers\Admin\CreatorController::class, 'index'])->name('creators.index');
    Route::get('/creators/{creator}', [\App\Http\Controllers\Admin\CreatorController::class, 'show'])->whereNumber('creator')->name('creators.show');
    Route::patch('/creators/{creator}', [\App\Http\Controllers\Admin\CreatorController::class, 'update'])->whereNumber('creator')->name('creators.update');
    Route::delete('/creators/{creator}', [\App\Http\Controllers\Admin\CreatorController::class, 'destroy'])->whereNumber('creator')->name('creators.destroy');
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/article', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::post('/article', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/event', [EventController::class, 'edit'])->name('events.edit');
    Route::post('/event', [EventController::class, 'store'])->name('events.store');
    Route::get('/project', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/project', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/snapshot', [SnapshotController::class, 'edit'])->name('snapshots.edit');
    Route::post('/snapshot', [SnapshotController::class, 'store'])->name('snapshots.store');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/password', [SettingsController::class, 'password'])->name('password.update');
    Route::get('/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::delete('/records/{type}/{id}', [RecordController::class, 'destroy'])->whereNumber('id')->name('records.destroy');
});
