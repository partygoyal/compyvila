<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\CompetitionRegistrationController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JudgeController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\WinnerController;
use App\Http\Controllers\Api\CertificatePdfController;
use App\Http\Controllers\Api\PublicVoteController;
use App\Http\Controllers\Api\NotificationController;

Route::post('/register', [
    AuthController::class,
    'register'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::get('/auth/google/redirect', [
    AuthController::class,
    'redirectToGoogle'
]);

Route::get('/auth/google/callback', [
    AuthController::class,
    'handleGoogleCallback'
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [
        AuthController::class,
        'me'
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    Route::post('/competition-registration', [
        CompetitionRegistrationController::class,
        'store'
    ]);

    Route::post('/submission', [
        SubmissionController::class,
        'store'
    ]);

    Route::get('/my-registrations', [
        DashboardController::class,
        'myRegistrations'
    ]);

    Route::get('/my-submissions', [
        DashboardController::class,
        'mySubmissions'
    ]);

    Route::get('/my-certificates', [
        DashboardController::class,
        'myCertificates'
    ]);

    Route::get('/judge/assignments', [
        JudgeController::class,
        'assignments'
    ]);

    Route::get('/judge/submissions', [
        JudgeController::class,
        'submissions'
    ]);

    Route::post('/judge/review', [
        JudgeController::class,
        'review'
    ]);

    Route::post('/judge/submissions/{submission}/shortlist', [
        JudgeController::class,
        'shortlist'
    ]);

    Route::get('/submission/{id}', [
        SubmissionController::class,
        'show'
    ]);

    Route::get(
        '/certificate/{id}/download',
        [CertificatePdfController::class, 'download']
    );

    Route::post('/profile/update', [
        AuthController::class,
        'updateProfile'
    ]);

    Route::get('/notifications', [
        NotificationController::class,
        'index'
    ]);

    Route::post('/notifications/{id}/read', [
        NotificationController::class,
        'markAsRead'
    ]);

    Route::post('/notifications/read-all', [
        NotificationController::class,
        'markAllAsRead'
    ]);

    Route::post('/public-votes', [
        PublicVoteController::class,
        'store'
    ]);

});

Route::get('/categories', [
    CompetitionController::class,
    'categories'
]);

Route::get('/competitions', [
    CompetitionController::class,
    'competitions'
]);

Route::get('/competitions/{slug}', [
    CompetitionController::class,
    'show'
]);


Route::get('/results', [
    ResultController::class,
    'leaderboard'
]);

Route::get('/winners', [
    ResultController::class,
    'winners'
]);

Route::get('/competitions/{competition}/results', [
    ResultController::class,
    'competitionResults'
]);

Route::get(
    '/competition/{slug}/results',
    [ResultController::class, 'leaderboard']
);

Route::get(
    '/competition/{slug}/winners',
    [WinnerController::class, 'competitionWinners']
);

Route::get(
    '/competition/{slug}/public-voting',
    [PublicVoteController::class, 'submissions']
);

Route::get(
    '/competition/{slug}/public-votes/leaderboard',
    [PublicVoteController::class, 'leaderboard']
);
