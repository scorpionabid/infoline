<?php

namespace App\Http\Controllers\Settings\Table;

use App\Domain\Entities\Column;
use App\Domain\Entities\ColumnChoice;
use App\Domain\Entities\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ColumnController extends Controller
{
    /**
     * Kateqoriyaya aid sütunları qaytarır
     */
    public function index($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $columns = $category->columns()->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'columns' => $columns
        ]);
    }
    
    /**
     * Sütun məlumatlarını qaytarır
     */
    public function show($id)
    {
        try {
            $column = Column::with('choices')->findOrFail($id);
            
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
     * Yeni sütun əlavə edir
     */
    public function store(Request $request)
    {
        try {
            Log::info('Column store request:', $request->all());

            // Əsas validasiya
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|min:2|max:255',
                'type' => 'required|string|in:text,number,date,select,textarea,file',
                'description' => 'nullable|string|max:1000',
                'required' => 'boolean',
                'input_limit' => 'nullable|integer|min:0',
                'end_date' => 'nullable|date',
                'choices' => 'required_if:type,select|array',
                'choices.*.value' => 'required_if:type,select|string|max:255',
                'choices.*.label' => 'required_if:type,select|string|max:255',
                'choices.*.is_default' => 'boolean'
            ]);

            DB::beginTransaction();

            $category = Category::findOrFail($validated['category_id']);
            Log::info('Found category:', ['id' => $category->id, 'name' => $category->name]);

            // Sütunun sırasını təyin et
            $lastOrder = $category->columns()->max('order') ?? 0;
            
            // Sütunu əlavə et
            $column = Column::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'required' => $request->boolean('required'),
                'end_date' => $validated['end_date'],
                'input_limit' => $validated['input_limit'],
                'order' => $lastOrder + 1,
                'is_active' => true
            ]);
            
            // Seçim tipi üçün seçimləri əlavə et
            if ($validated['type'] === 'select' && isset($validated['choices'])) {
                foreach ($validated['choices'] as $index => $choice) {
                    ColumnChoice::create([
                        'column_id' => $column->id,
                        'value' => $choice['value'],
                        'label' => $choice['label'] ?? $choice['value'],
                        'sort_order' => $index,
                        'is_default' => $choice['is_default'] ?? false
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla əlavə edildi',
                'column' => $column->load(['category', 'choices'])
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
     * Sütunu yeniləyir
     */
    public function update(Request $request, $id)
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
                'type' => 'required|string|in:text,number,date,select,textarea,file',
                'description' => 'nullable|string|max:1000',
                'required' => 'boolean',
                'input_limit' => 'nullable|integer|min:0',
                'end_date' => 'nullable|date',
                'choices' => 'required_if:type,select|array',
                'choices.*.value' => 'required_if:type,select|string|max:255',
                'choices.*.label' => 'required_if:type,select|string|max:255',
                'choices.*.is_default' => 'boolean'
            ]);

            DB::beginTransaction();

            // Sütunu yenilə
            $column->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'required' => $request->boolean('required'),
                'end_date' => $validated['end_date'],
                'input_limit' => $validated['input_limit']
            ]);
            
            // Seçim tipi üçün seçimləri yenilə
            if ($validated['type'] === 'select') {
                // Köhnə seçimləri sil
                $column->choices()->delete();
                
                // Yeni seçimləri əlavə et
                if (isset($validated['choices'])) {
                    foreach ($validated['choices'] as $index => $choice) {
                        ColumnChoice::create([
                            'column_id' => $column->id,
                            'value' => $choice['value'],
                            'label' => $choice['label'] ?? $choice['value'],
                            'sort_order' => $index,
                            'is_default' => $choice['is_default'] ?? false
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla yeniləndi',
                'column' => $column->fresh()->load('choices')
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $column = Column::findOrFail($id);
            
            // Sütuna aid bütün seçimləri sil
            $column->choices()->delete();
            
            // Sütunu sil
            $column->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütun uğurla silindi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
    
    /**
     * Sütun statusunu dəyişir
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $column = Column::findOrFail($id);
            $column->is_active = $request->boolean('status');
            $column->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütun statusu uğurla yeniləndi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating column status:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun statusu yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun son tarixini yeniləyir
     */
    public function updateDeadline(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'end_date' => 'nullable|date'
            ]);
            
            $column = Column::findOrFail($id);
            $column->end_date = $validated['end_date'];
            $column->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütun son tarixi uğurla yeniləndi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating column deadline:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun son tarixi yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun limit dəyərini yeniləyir
     */
    public function updateLimit(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'input_limit' => 'nullable|integer|min:0'
            ]);
            
            $column = Column::findOrFail($id);
            $column->input_limit = $validated['input_limit'];
            $column->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütun limiti uğurla yeniləndi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating column limit:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun limiti yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun seçimlərini qaytarır
     */
    public function getChoices($id)
    {
        try {
            $column = Column::findOrFail($id);
            $choices = $column->choices()->orderBy('sort_order')->get();
            
            return response()->json([
                'success' => true,
                'choices' => $choices
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting column choices:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütun seçimləri əldə edilərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun seçimi əlavə edir
     */
    public function storeChoice(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'value' => 'required|string|max:255',
                'label' => 'required|string|max:255',
                'is_default' => 'boolean'
            ]);
            
            $column = Column::findOrFail($id);
            
            if ($column->type !== 'select') {
                return response()->json([
                    'success' => false,
                    'message' => 'Yalnız seçim tipindəki sütunlara seçim əlavə etmək mümkündür'
                ], 400);
            }
            
            // Seçimin sırasını təyin et
            $lastOrder = $column->choices()->max('sort_order') ?? 0;
            
            $choice = ColumnChoice::create([
                'column_id' => $column->id,
                'value' => $validated['value'],
                'label' => $validated['label'],
                'sort_order' => $lastOrder + 1,
                'is_default' => $request->boolean('is_default')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Seçim uğurla əlavə edildi',
                'choice' => $choice
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing column choice:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Seçim əlavə edilərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun seçimini yeniləyir
     */
    public function updateChoice(Request $request, $columnId, $choiceId)
    {
        try {
            $validated = $request->validate([
                'value' => 'required|string|max:255',
                'label' => 'required|string|max:255',
                'is_default' => 'boolean'
            ]);
            
            $column = Column::findOrFail($columnId);
            $choice = $column->choices()->findOrFail($choiceId);
            
            $choice->update([
                'value' => $validated['value'],
                'label' => $validated['label'],
                'is_default' => $request->boolean('is_default')
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Seçim uğurla yeniləndi',
                'choice' => $choice->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating column choice:', [
                'column_id' => $columnId,
                'choice_id' => $choiceId,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Seçim yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütun seçimini silir
     */
    public function destroyChoice($columnId, $choiceId)
    {
        try {
            $column = Column::findOrFail($columnId);
            $choice = $column->choices()->findOrFail($choiceId);
            
            $choice->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Seçim uğurla silindi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting column choice:', [
                'column_id' => $columnId,
                'choice_id' => $choiceId,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Seçim silinərkən xəta baş verdi'
            ], 500);
        }
    }
    
    /**
     * Sütunların sırasını yeniləyir
     */
    public function updateOrder(Request $request, $categoryId)
    {
        try {
            $validated = $request->validate([
                'order' => 'required|array',
                'order.*' => 'required|integer|exists:columns,id'
            ]);
            
            DB::beginTransaction();
            
            $category = Category::findOrFail($categoryId);
            
            foreach ($validated['order'] as $index => $columnId) {
                $column = Column::findOrFail($columnId);
                
                if ($column->category_id != $category->id) {
                    throw new \Exception('Sütun bu kateqoriyaya aid deyil');
                }
                
                $column->order = $index + 1;
                $column->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sütunların sırası uğurla yeniləndi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating column order:', [
                'category_id' => $categoryId,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sütunların sırası yenilənərkən xəta baş verdi'
            ], 500);
        }
    }
    private function handleColumnOperation(callable $operation, $message, $errorMessage)
    {
        try {
            DB::beginTransaction();
            
            $result = $operation();
            
            DB::commit();
        
            return response()->json([
            'success' => true,
            'message' => $message,
            'result' => $result
        ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($errorMessage, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}