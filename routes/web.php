<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GanttChartController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\KindController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VersionController;
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

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/search-categories', [CategoryController::class, 'search'])->name('search-categories');
    Route::get('/create-category', [CategoryController::class, 'create'])->name('create-category');
    Route::post('/create-category', [CategoryController::class, 'store']);
    Route::get('/edit-category/{category_id}', [CategoryController::class, 'edit'])->name('edit-category');
    Route::put('/edit-category/{category_id}', [CategoryController::class, 'update']);
    Route::delete('/delete-category/{category_id}', [CategoryController::class, 'delete'])->name('delete-category');
  
    Route::get('/milestones', [MilestoneController::class, 'index'])->name('milestones');
    Route::get('/search-milestones', [MilestoneController::class, 'search'])->name('search-milestones');
    Route::get('/create-milestone', [MilestoneController::class, 'create'])->name('create-milestone');
    Route::post('/create-milestone', [MilestoneController::class, 'store']);
    Route::get('/edit-milestone/{milestone_id}', [MilestoneController::class, 'edit'])->name('edit-milestone');
    Route::put('/edit-milestone/{milestone_id}', [MilestoneController::class, 'update']);
    Route::delete('/delete-milestone/{milestone_id}', [MilestoneController::class, 'delete'])->name('delete-milestone');
  
    Route::get('/kinds', [KindController::class, 'index'])->name('kinds');
    Route::get('/search-kinds', [KindController::class, 'search'])->name('search-kinds');
    Route::get('/create-kind', [KindController::class, 'create'])->name('create-kind');
    Route::post('/create-kind', [KindController::class, 'store']);
    Route::get('/edit-kind/{kind_id}', [KindController::class, 'edit'])->name('edit-kind');
    Route::put('/edit-kind/{kind_id}', [KindController::class, 'update']);
    Route::delete('/delete-kind/{kind_id}', [KindController::class, 'delete'])->name('delete-kind');
  
    Route::get('/statuses', [StatusController::class, 'index'])->name('statuses');
    Route::get('/search-statuses', [StatusController::class, 'search'])->name('search-statuses');
    Route::get('/create-status', [StatusController::class, 'create'])->name('create-status');
    Route::post('/create-status', [StatusController::class, 'store']);
    Route::get('/edit-status/{status_id}', [StatusController::class, 'edit'])->name('edit-status');
    Route::put('/edit-status/{status_id}', [StatusController::class, 'update']);
    Route::delete('/delete-status/{status_id}', [StatusController::class, 'delete'])->name('delete-status');
  
    Route::get('/versions', [VersionController::class, 'index'])->name('versions');
    Route::get('/search-versions', [VersionController::class, 'search'])->name('search-versions');
    Route::get('/create-version', [VersionController::class, 'create'])->name('create-version');
    Route::post('/create-version', [VersionController::class, 'store']);
    Route::get('/edit-version/{version_id}', [VersionController::class, 'edit'])->name('edit-version');
    Route::put('/edit-version/{version_id}', [VersionController::class, 'update']);
    Route::delete('/delete-version/{version_id}', [VersionController::class, 'delete'])->name('delete-version');
});

require __DIR__.'/auth.php';
