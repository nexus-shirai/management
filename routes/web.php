<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GanttChartController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProjectController;
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

Route::get('/', [DashboardController::class, 'index'])->name('index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/project/{project_id}', [ProjectController::class, 'index'])->name('project');
Route::get('/issues', [IssueController::class, 'index'])->name('issues');
Route::get('/create-issue', [IssueController::class, 'create'])->name('create-issue');
Route::get('/edit-issue/{issue_id}', [IssueController::class, 'edit'])->name('edit-issue');
Route::get('/view-issue/{issue_id}', [IssueController::class, 'view'])->name('view-issue');
Route::get('/board', [BoardController::class, 'index'])->name('board');
Route::get('/gantt-chart', [GanttChartController::class, 'index'])->name('gantt-chart');

require __DIR__.'/auth.php';
