<?php

namespace App\Http\Controllers\Settings\Table;
use AppHttpControllersController;

use App\Http\Controllers\Controller;
use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableSettingsController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Authorization check
            if (!auth()->user()->isSuperAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Bu bölməyə giriş icazəniz yoxdur.');
            }

            // Get categories with column count
            $categories = Category::withCount('columns')
                ->orderBy('name')
                ->get();

            // Get selected category and its columns
            $selectedCategory = null;
            $columns = collect();

            if ($request->has('category')) {
                $selectedCategory = Category::with(['columns' => function($query) {
                    $query->orderBy('name');
                }])->findOrFail($request->category);
                
                $columns = $selectedCategory->columns;
            } elseif ($categories->isNotEmpty()) {
                $selectedCategory = Category::with(['columns' => function($query) {
                    $query->orderBy('name');
                }])->find($categories->first()->id);
            
                $columns = $selectedCategory->columns;
            }
            return view('pages.settings.table.index', compact('categories', 'selectedCategory', 'columns'));
        } catch (\Exception $e) {
            Log::error('Settings Table Error: ' . $e->getMessage());
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();
            
            $category = Category::create($validated);
            
            DB::commit();

            return redirect()
                ->route('settings.table', ['category' => $category->id])
                ->with('success', 'Kateqoriya uğurla əlavə edildi');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category Store Error: ' . $e->getMessage());
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function storeColumn(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'data_type' => 'required|in:text,number,date,select,multiselect,file',
                'end_date' => 'nullable|date',
                'input_limit' => 'nullable|integer|min:1',
                'choices' => 'required_if:data_type,select,multiselect|array',
                'choices.*' => 'required_string|max:255',
                'validation_rules' => 'nullable|array'
            ]);

            DB::beginTransaction();

            // Sütunu yarat
            $column = Column::create([
                'name' => $validated['name'],
                'data_type' => $validated['data_type'],
                'end_date' => $validated['end_date'] ?? null,
                'input_limit' => $validated['input_limit'] ?? null,
                'category_id' => $validated['category_id'],
                'validation_rules' => $validated['validation_rules'] ?? null
            ]);

            // Seçim variantlarını əlavə et
            if (in_array($validated['data_type'], ['select', 'multiselect']) && !empty($validated['choices'])) {
                foreach ($validated['choices'] as $choice) {
                    $column->choices()->create(['value' => $choice]);
                }
            }

            DB::commit();

            return redirect()
                ->route('settings.table', ['category' => $validated['category_id']])
                ->with('success', 'Sütun uğurla əlavə edildi');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Column Store Error: ' . $e->getMessage());
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function updateColumn(Request $request, Column $column)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'data_type' => 'required|in:text,number,date,select,multiselect,file',
                'end_date' => 'nullable|date',
                'input_limit' => 'nullable|integer|min:1',
                'choices' => 'required_if:data_type,select,multiselect|array',
                'choices.*' => 'required_string|max:255',
                'validation_rules' => 'nullable|array'
            ]);

            DB::beginTransaction();

            // Sütunu yenilə
            $column->update([
                'name' => $validated['name'],
                'data_type' => $validated['data_type'],
                'end_date' => $validated['end_date'] ?? null,
                'input_limit' => $validated['input_limit'] ?? null,
                'validation_rules' => $validated['validation_rules'] ?? null
            ]);

            // Seçim variantlarını yenilə
            if (in_array($validated['data_type'], ['select', 'multiselect'])) {
                $column->choices()->delete();  // Köhnələri sil
                foreach ($validated['choices'] as $choice) {
                    $column->choices()->create(['value' => $choice]);
                }
            }

            DB::commit();

            return redirect()
                ->route('settings.table', ['category' => $column->category_id])
                ->with('success', 'Sütun uğurla yeniləndi');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Column Update Error: ' . $e->getMessage());
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }
    // Kategoriya silmə
    public function destroyCategory(Category $category)
    {
        try {
            if ($category->columns()->exists()) {
                return back()->with('error', 'Bu kateqoriyaya aid sütunlar var. Əvvəlcə sütunları silin.');
            }

        $category->delete();
        return redirect()->route('settings.table')
            ->with('success', 'Kateqoriya uğurla silindi');
    } catch (\Exception $e) {
        Log::error('Category Delete Error: ' . $e->getMessage());
        return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
    }
}

// Sütun silmə 
    public function destroyColumn(Column $column)
    {
        try {
            if ($column->dataValues()->exists()) {
                return back()->with('error', 'Bu sütuna aid məlumatlar var. Silmək Mümkün deyil.');
            }

            $column->delete();
            return back()->with('success', 'Sütun uğurla silindi');
        } catch (\Exception $e) {
            Log::error('Column Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }
}