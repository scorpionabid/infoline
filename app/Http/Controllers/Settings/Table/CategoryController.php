<?php

namespace App\Http\Controllers\Settings\Table;

use App\Domain\Entities\Category;
use App\Domain\Entities\CategoryAssignment;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Kateqoriyalar səhifəsini göstərir
     * 
     * @deprecated Əvəzinə TableSettingsController::index() istifadə edin
     */
    public function index(Request $request)
    {
    // Təzə əlavə olunmuş kateqoriyanı yenidən yükləyin
        $categories = Category::with(['columns' => function($query) {
            $query->orderBy('order');
        }])->get();
    
    // Seçilmiş kateqoriyanı təyin edin
        $selectedCategory = $request->has('category') 
            ? Category::findOrFail($request->input('category'))
            : null;
    
    // Sütunları yükləyin
        $columns = $selectedCategory 
            ? $selectedCategory->columns()->orderBy('order')->get() 
            : collect();

        return view('pages.settings.table.index', compact('categories', 'selectedCategory', 'columns'));
    }

    /**
     * Bütün kateqoriyaları qaytarır
     */
    public function all()
    {
        $categories = Category::with('columns')->get();
        return response()->json($categories);
    }

    /**
     * Belirli bir kateqoriyanı göstərir
     */
    public function show($id)
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

    /**
     * Yeni kateqoriya əlavə edir
     */
    public function store(Request $request)
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
            if ($validated['assigned_type'] === 'all') {
                CategoryAssignment::create([
                    'category_id' => $category->id,
                    'assigned_type' => 'all',
                    'assigned_id' => null
                ]);
            } else {
                foreach ($validated['assigned_ids'] as $id) {
                    CategoryAssignment::create([
                        'category_id' => $category->id,
                        'assigned_type' => $validated['assigned_type'],
                        'assigned_id' => $id
                    ]);
                }
            }

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

    /**
     * Kateqoriyanı yeniləyir
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:1000',
            'assigned_type' => 'required|in:all,sector,school',
            'assigned_ids' => 'required_unless:assigned_type,all|array'
        ]);

        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);
            
            // Kateqoriyanı yenilə
            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);
            
            // Köhnə əlaqələri sil
            $category->assignments()->delete();
            
            // Yeni əlaqələri əlavə et
            if ($validated['assigned_type'] === 'all') {
                CategoryAssignment::create([
                    'category_id' => $category->id,
                    'assigned_type' => 'all',
                    'assigned_id' => null
                ]);
            } else {
                foreach ($validated['assigned_ids'] as $id) {
                    CategoryAssignment::create([
                        'category_id' => $category->id,
                        'assigned_type' => $validated['assigned_type'],
                        'assigned_id' => $id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya uğurla yeniləndi',
                'category' => $category->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating category:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya yenilənərkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Kateqoriyanı silir
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);

            // Kateqoriyaya aid bütün sütunları sil
            $category->columns()->delete();
            
            // Kateqoriyaya aid bütün əlaqələri sil
            $category->assignments()->delete();
            
            // Kateqoriyanı sil
            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya uğurla silindi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting category:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya silinərkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Kateqoriya statusunu dəyişir
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->is_active = $request->boolean('status');
            $category->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya statusu uğurla yeniləndi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category status:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya statusu yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Kateqoriya əlaqələndirmələrini qaytarır
     */
    public function getAssignments($id)
    {
        try {
            $category = Category::findOrFail($id);
            $assignments = CategoryAssignment::where('category_id', $id)->get();
            
            return response()->json([
                'success' => true,
                'assignments' => $assignments
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting category assignments:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya əlaqələndirmələri tapılmadı'
            ], 404);
        }
    }
    
    /**
     * Kateqoriyanı kopyalayır
     */
    public function clone(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $sourceCategory = Category::with(['columns', 'assignments'])->findOrFail($id);
            
            // Yeni adı əldə et
            $newName = $request->input('name') ?? $sourceCategory->name . ' (Copy)';
            
            // Yeni kateqoriya yarat
            $newCategory = Category::create([
                'name' => $newName,
                'description' => $sourceCategory->description,
                'is_active' => true
            ]);
            
            // Əlaqələri kopyala
            foreach ($sourceCategory->assignments as $assignment) {
                CategoryAssignment::create([
                    'category_id' => $newCategory->id,
                    'assigned_type' => $assignment->assigned_type,
                    'assigned_id' => $assignment->assigned_id
                ]);
            }
            
            // Sütunları kopyala (seçimləri də daxil olmaqla)
            foreach ($sourceCategory->columns as $column) {
                $newColumn = $newCategory->columns()->create([
                    'name' => $column->name,
                    'description' => $column->description,
                    'type' => $column->type,
                    'required' => $column->required,
                    'options' => $column->options,
                    'validation_rules' => $column->validation_rules,
                    'order' => $column->order,
                    'is_active' => $column->is_active,
                    'end_date' => $column->end_date,
                    'input_limit' => $column->input_limit
                ]);
                
                // Seçimləri kopyala
                if ($column->choices) {
                    foreach ($column->choices as $choice) {
                        $newColumn->choices()->create([
                            'value' => $choice->value,
                            'label' => $choice->label,
                            'sort_order' => $choice->sort_order,
                            'is_default' => $choice->is_default
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya uğurla kopyalandı',
                'category' => $newCategory->load('columns', 'assignments')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cloning category:', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya kopyalanarkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}