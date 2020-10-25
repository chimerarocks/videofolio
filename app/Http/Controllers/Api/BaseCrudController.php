<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{
    protected abstract function model();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());
        $model = $this->model()::create($validateData);
        $model->refresh();
        return $model;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        $model = $this->findOrFail($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $model->update($validatedData);
        return $model;
    }

    public function destroy($id)
    {
        $model = $this->findOrFail($id);
        $model->delete();
        return response()->noContent();
    }
}
