<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GanttChartController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/create-project', [ProjectController::class, 'create'])->name('create-project');
    Route::post('/create-project', [ProjectController::class, 'store']);
    Route::get('/view-project/{project_id}', [ProjectController::class, 'view'])->name('view-project');
    Route::get('/edit-project/{project_id}', [ProjectController::class, 'edit'])->name('edit-project');
    Route::put('/edit-project/{project_id}', [ProjectController::class, 'update']);
    Route::delete('/delete-project/{project_id}', [ProjectController::class, 'delete'])->name('delete-project');
    Route::get('/issues', [IssueController::class, 'index'])->name('issues');
    Route::get('/create-issue', [IssueController::class, 'create'])->name('create-issue');
    Route::post('/create-issue', [IssueController::class, 'store']);
    Route::get('/view-issue/{issue_id}', [IssueController::class, 'view'])->name('view-issue');
    Route::get('/edit-issue/{issue_id}', [IssueController::class, 'edit'])->name('edit-issue');
    Route::get('/board', [BoardController::class, 'index'])->name('board');
    Route::get('/gantt-chart', [GanttChartController::class, 'index'])->name('gantt-chart');
    Route::get('/create-user', [UserController::class, 'create'])->name('create-user');
    Route::post('/create-user', [UserController::class, 'store']);
});

require __DIR__.'/auth.php';
