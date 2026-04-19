<?php


namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Articles\CategoryResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Articles\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/v1/categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('articles')->orderBy('name')->get();

        return $this->successResponse(CategoryResource::collection($categories));
    }

    /**
     * GET /api/v1/categories/{category}
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('articles');

        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * POST /api/v1/categories
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'],
        ]);

        $category = Category::create($data);

        return $this->createdResponse(new CategoryResource($category), 'Kategori berhasil dibuat.');
    }

    /**
     * PUT /api/v1/categories/{category}
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($category->id)],
        ]);

        $category->update($data);

        return $this->successResponse(new CategoryResource($category), 'Kategori berhasil diperbarui.');
    }

    /**
     * DELETE /api/v1/categories/{category}
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $category->articles()->detach();
        $category->delete();

        return $this->noContentResponse();
    }
}
