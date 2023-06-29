<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class GanttChartController extends Controller
{
    public function index() {
        return Inertia::render('GanttChart');
    }
}
