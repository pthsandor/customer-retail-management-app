<?php

use App\Http\Resources\TodayTaskResource;
use App\Http\Resources\UpcomingResource;
use App\Models\Today;
use App\Models\Upcoming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ** Upcoming Task *//

// Get all the upcoming task
Route::get('/upcoming', function(){
    $upcoming = Upcoming::all();
    return UpcomingResource::collection($upcoming);
});


// Add a new task
Route::post('/upcoming', function(Request $request){
        return Upcoming::create([
            'title' => $request->title,
            'taskID' => $request->taskID,
            'waiting' => $request->waiting
            ]);
});


// Delete upcoming task
Route::delete('/upcoming/{taskID}', function($taskID){
    DB::table('upcomings')->where('taskID', $taskID)->delete();

        return 204;
});



// ** Today Task *//

//Get all task
Route::get("/dailytask", function(){
    $todaytask = Today::all();
    return TodayTaskResource::collection($todaytask);
});

// Add a task
Route::post('/dailytask', function(Request $request){
    return Today::create([
        'id' => $request->id,
        'title' => $request->title,
        'taskID' => $request->taskID
        ]);
});


// Delete Today Task
Route::delete('/dailytask/{taskID}', function($taskID){
    DB::table('todays')->where('taskID', $taskID)->delete();

        return 204;
});