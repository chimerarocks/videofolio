<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BaseCrudController;
use App\Models\Category;
use Illuminate\Http\Request;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BaseCrudController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $category = Category::create($request->all());
        $category->refresh(); // In order to all fields be returned
        return $category;
    }

    public function show(Category $category) //Route model binding
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        $category->update($request->all());
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
