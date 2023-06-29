<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class IssueController extends Controller
{
    public function index() {
        return Inertia::render('Issues');
    }

    public function create() {
        return Inertia::render('EditIssue', [
            "type" => "Create"
        ]);
    }

    public function edit() {
        return Inertia::render('EditIssue', [
            "type" => "Edit"
        ]);
    }

    public function view() {
        return Inertia::render('EditIssue', [
            "type" => "View"
        ]);
    }
}
