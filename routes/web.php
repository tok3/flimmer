<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoRoomsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('rooms/test', [VideoRoomsController::class, 'test']);

// video neu
Route::post('/api/room/create', [VideoRoomsController::class, 'createRoom']);
Route::post('/api/room/token', [VideoRoomsController::class, 'generateToken']);
Route::get('/api/rooms', [VideoRoomsController::class, 'listRooms']);


//Route::middleware(['auth'])->prefix('room')->group(function() {
    Route::get('join/{roomName}', [VideoRoomsController::class, 'joinRoom']);
    Route::get('rooms/list', [VideoRoomsController::class, 'listRooms']);
    Route::post('room/create', [VideoRoomsController::class, 'createRoom']);

//});



Route::post('/mute-participant', [VideoRoomsController::class, 'muteParticipant']);
Route::post('/trigger-mute-all', [VideoRoomsController::class, 'triggerMuteAllEvent']);
Route::post('/trigger-toggle-mute', [VideoRoomsController::class, 'triggerToggleMuteEvent']);
Route::post('/trigger-hand-raise', [VideoRoomsController::class, 'triggerHandRaiseEvent']);
Route::post('/cleanup-hand-raise', [VideoRoomsController::class, 'cleanHandRaiseEvent']); // on disconnect

Route::post('/trigger-staging', [VideoRoomsController::class, 'triggerStagingEvent']);
Route::post('/cleanup-staging', [VideoRoomsController::class, 'cleanToggleCache']);

//Route::post('/cleanup-hand-raise', [VideoRoomsController::class, 'cleanToggleCache']); // on disconnect

Route::get('/cache-test', [VideoRoomsController::class, 'testCache']);
Route::get('/room/{roomId}/hand-statuses', [VideoRoomsController::class, 'getHandStatuses']);
Route::get('/room/{roomId}/staged-statuses', [VideoRoomsController::class, 'getStagedStatuses']);

Route::get('/test-cache', [VideoRoomsController::class, 'testCache']);


//Route::get('/trigger-toggle-mute', [VideoRoomsController::class, 'triggerToggleMuteEvent']);


Route::get('/test-pusher', function() {
    event(new \App\Events\TestEvent('Hello World'));
    return 'Event gesendet!';
});

Route::get('/', function () {
return redirect('admin/login');
   // return view('welcome-vue');
});
