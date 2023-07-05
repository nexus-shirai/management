<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    protected $service;

    function __construct(CategoryService $service) {
        $this->service = $service;
    }

    public function index() {
        return Inertia::render("Categories");
    }

    public function search() {
        $categories = $this->service->getCategories();

        return response()->json($categories);
    }
    
    public function create() {
        return Inertia::render("EditCategory", [
            "type" => "Create"
        ]);
    }

    public function store(CategoryRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route('categories')
            ->with("message", "カテゴリーを作成しました。");
    }

    public function edit(Request $request) {
        $data["category_id"] = $request->category_id;
        $categoryData = $this->service->getCategoryData($data);

        return Inertia::render("EditCategory", [
            "type" => "Edit",
            "category" => $categoryData
        ]);
    }

    public function update(CategoryRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('categories')
            ->with("message", "カテゴリーを更新しました。");
    }

    public function delete(Request $request) {
        $categoryId = $request->category_id;
        DB::beginTransaction();
        $this->service->delete($categoryId);
        DB::commit();

        return redirect()->route('categories')
            ->with("message", "カテゴリーを削除しました。");
    }
}
