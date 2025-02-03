<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\Category;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\API\V1\Category\StoreCategoryRequest;
use App\Http\Requests\API\V1\Category\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                return $this->sendError('Bu əməliyyat üçün icazəniz yoxdur', [], 403);
            }
            return $next($request);
        });
    }

    public function index(): JsonResponse
    {
        $categories = Category::withCount('columns')->get();
        
        return $this->sendResponse($categories, 'Kateqoriyalar uğurla əldə edildi');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());
        
        return $this->sendResponse($category, 'Kateqoriya uğurla yaradıldı', 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load('columns');
        return $this->sendResponse($category, 'Kateqoriya uğurla əldə edildi');
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());
        
        return $this->sendResponse($category, 'Kateqoriya uğurla yeniləndi');
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->columns()->exists()) {
            return $this->sendError('Aktiv sütunları olan kateqoriya silinə bilməz', [], 422);
        }

        $category->delete();
        
        return $this->sendResponse(null, 'Kateqoriya uğurla silindi');
    }
}