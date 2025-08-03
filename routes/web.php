<?php

use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\SubscriptionController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [StoryController::class, 'index'])->name('home');
Route::get('/stories/{id}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/newCreate', function () {
    return view('create');
})->name('newCreate');



//Route::get('/stories/newCreate', [StoryController::class, 'create'])->name('stories.newCreate');
Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
Route::get('/chat', [ChatBotController::class, 'index'])->name('chat');
Route::post('/chat', [ChatBotController::class, 'sendMessage'])->name('chat.send');


 Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    
    Route::post('/projects/{id}/query', [ProjectController::class, 'generateQuery'])->name('projects.generateQuery');

Route::get('/generate-voice', function () {
    return view('generate-voice');
});

Route::post('/generate-voice', [LessonController::class, 'generateVoice'])->name('generate.voice');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/projects/canvas', [ProjectController::class, 'canvas'])->name('projects.canvas');
});

require __DIR__.'/auth.php';

Route::post('/chat/send', [ChatController::class, 'send'])->middleware('chat.limit');
Route::post('/payhere/webhook', [PaymentController::class, 'webhook']);

Route::middleware('auth')->group(function () {
Route::post('/chat/voice', [ChatController::class, 'handleVoice'])->middleware(['voice.chat.paid']);
});
// routes/web.php
Route::get('/subscribe', [SubscriptionController::class, 'showForm']);
Route::post('/subscribe/payhere', [SubscriptionController::class, 'payWithPayHere']);
Route::post('/subscribe/notify', [SubscriptionController::class, 'handlePayHereWebhook']); // IPN

Route::post('/device/authenticate', [DeviceController::class, 'authenticate']);
