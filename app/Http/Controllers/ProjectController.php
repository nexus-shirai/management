<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index() {
        return Inertia::render("Project");
    }
}
