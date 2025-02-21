<?php

namespace App\Http\Controllers\Settings\Table;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\School;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

        return view('pages.settings.table.index', compact('categories', 'selectedCategory', 'columns'));
    }

    /**
     * Bütün kateqoriyaları qaytarır
     */
    public function categories()
    {
        $categories = Category::with('columns')->get();
        return response()->json($categories);
    }

    /**
     * Yeni kateqoriya əlavə edir
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya uğurla əlavə edildi',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya əlavə edilərkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Kateqoriyanı silir
     */
    public function destroyCategory($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);

            // Kateqoriyaya aid bütün sütunları sil
            $category->columns()->delete();
            
            // Kateqoriyanı sil
            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kateqoriya uğurla silindi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kateqoriya silinərkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Yeni sütun əlavə edir
     */
    public function storeColumn(Request $request)
    {
        try {
            Log::info('Column store request:', $request->all());

            // Əsas validasiya
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|min:2|max:255',
                'data_type' => 'required|string|in:text,number,date,select,file',
                'description' => 'nullable|string|max:1000',
                'is_required' => 'boolean',
                'input_limit' => 'nullable|integer|min:0',
                'end_date' => 'nullable|date'
            ]);

            // Seçim tipi üçün əlavə validasiya
            if ($request->input('data_type') === 'select') {
                $request->validate([
                    'options' => 'required|array|min:1',
                    'options.*' => 'required|string|max:255'
                ]);
                $validated['options'] = $request->input('options');
            }

            DB::beginTransaction();

            $category = Category::findOrFail($validated['category_id']);
            Log::info('Found category:', ['id' => $category->id, 'name' => $category->name]);

            // Sütunun sırasını təyin et
            $lastOrder = $category->columns()->max('order') ?? 0;
            $validated['order'] = $lastOrder + 1;

            // Boolean dəyərləri düzəlt
            $validated['is_required'] = $request->boolean('is_required');

            // category_id-ni sil, çünki create metodunda lazım deyil
            unset($validated['category_id']);

            Log::info('Creating column with data:', $validated);

            // Sütunu əlavə et
            $column = $category->columns()->create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla əlavə edildi',
                'column' => $column->load('category')
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating column:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sütun əlavə edilərkən xəta baş verdi',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Sütun məlumatlarını qaytarır
     */
    public function showColumn($id)
    {
        try {
            $column = Column::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'column' => $column
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing column:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun tapılmadı'
            ], 404);
        }
    }

    /**
     * Sütunu yeniləyir
     */
    public function updateColumn(Request $request, $id)
    {
        try {
            Log::info('Column update request:', [
                'id' => $id,
                'data' => $request->all()
            ]);

            // Sütunu tap
            $column = Column::findOrFail($id);

            // Əsas validasiya
            $validated = $request->validate([
                'name' => 'required|string|min:2|max:255',
                'data_type' => 'required|string|in:text,number,date,select,file',
                'description' => 'nullable|string|max:1000',
                'is_required' => 'boolean',
                'input_limit' => 'nullable|integer|min:0',
                'end_date' => 'nullable|date'
            ]);

            // Seçim tipi üçün əlavə validasiya
            if ($request->input('data_type') === 'select') {
                $request->validate([
                    'options' => 'required|array|min:1',
                    'options.*' => 'required|string|max:255'
                ]);
                $validated['options'] = $request->input('options');
            }

            // Boolean dəyərləri düzəlt
            $validated['is_required'] = $request->boolean('is_required');

            // Sütunu yenilə
            $column->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla yeniləndi',
                'column' => $column->fresh()
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating column:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun yenilənərkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Sütunu silir
     */
    public function destroyColumn($id)
    {
        try {
            $column = Column::findOrFail($id);
            
            // Sütunu sil
            $column->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla silindi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting column:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun silinərkən xəta baş verdi'
            ], 500);
        }
    }
}