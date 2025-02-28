<?php

namespace App\Http\Controllers\Settings\Table;

use App\Domain\Entities\Category;
use App\Domain\Entities\CategoryAssignment;
use App\Domain\Entities\Column;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableSettingsController extends Controller
{
    /**
     * Cədvəl ayarları səhifəsini göstərir
     */
    public function index(Request $request)
    {
        $categories = Category::with(['columns' => function($query) {
            $query->orderBy('order');
        }])->get();

        $sectors = Sector::all();
        $schools = School::all();

        $selectedCategory = null;
        $columns = collect();

        if ($categoryId = $request->query('category')) {
            $selectedCategory = Category::with(['columns' => function($query) {
                $query->orderBy('order');
            }])->find($categoryId);

            if ($selectedCategory) {
                $columns = $selectedCategory->columns;
            }
        }

        return view('pages.settings.table.table', compact('categories', 'selectedCategory', 'columns', 'sectors', 'schools'));
    }
    public function getAllCategories()
    {
        $categories = Category::with('columns')->get();
        return response()->json($categories);
    }

    public function showCategory($id)
    {
        try {
        $category = Category::with('columns')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'category' => $category
        ]);
        } catch (\Exception $e) {
            Log::error('Error showing category:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya tapılmadı'
            ], 404);
        }
    }
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:1000',
            'assigned_type' => 'required|in:all,sector,school',
            'assigned_ids' => 'required_unless:assigned_type,all|array'
        ]);

        try {
        DB::beginTransaction();

        // Kateqoriyanı yarat
        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => true
        ]);
        
        // Əlaqələndirmələri əlavə et
        $this->syncCategoryAssignments($category, $validated['assigned_type'], $validated['assigned_ids'] ?? []);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Kateqoriya uğurla əlavə edildi',
            'category' => $category
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating category:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Kateqoriya əlavə edilərkən xəta baş verdi',
            'errors' => [$e->getMessage()]
        ], 500);
    }
}

// Yeni yardımçı metod
private function syncCategoryAssignments($category, $assignedType, $assignedIds = [])
{
    // Köhnə əlaqələri sil
    $category->assignments()->delete();
    
    // Yeni əlaqələri əlavə et
    if ($assignedType === 'all') {
        CategoryAssignment::create([
            'category_id' => $category->id,
            'assigned_type' => 'all',
            'assigned_id' => null
        ]);
    } else {
        foreach ($assignedIds as $id) {
            CategoryAssignment::create([
                'category_id' => $category->id,
                'assigned_type' => $assignedType,
                'assigned_id' => $id
            ]);
        }
    }
}
}
