<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function update(Request $request, Category $category)
{
    // JSON’u zorla al, yoksa form/all
    $input = $request->json()->all();
    if (empty($input)) {
        $input = $request->all();
    }

    // (İstersen debug)
    // \Log::info(['raw'=>$request->getContent(), 'parsed_json'=>$request->json()->all(), 'parsed_all'=>$request->all()]);

    $validated = validator($input, [
        'name'        => 'sometimes|required|string|max:255',
        'description' => 'sometimes|nullable|string|max:500',
    ])->validate();

    $category->fill($validated);

    if (! $category->isDirty()) {
        return response()->json([
            'updated' => false,
            'message' => 'Güncellenecek alan yok veya gelen değerler aynı.',
            'data'    => $category
        ]);
    }

    $category->save();

    return response()->json($category->fresh());
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('products');
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
