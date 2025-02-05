<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\DataValue;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\API\V1\DataValue\StoreDataValueRequest;
use App\Http\Requests\API\V1\DataValue\BulkUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataValueController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = DataValue::with(['school', 'column'])
            ->latest();

        // Kateqoriyaya görə filter
        if ($request->has('category_id')) {
            $query->whereHas('column', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Məktəbə görə filter
        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        $dataValues = $query->get();
        
        return $this->sendResponse($dataValues, 'Məlumatlar uğurla əldə edildi');
    }

    public function store(StoreDataValueRequest $request): JsonResponse
    {
        $dataValue = DataValue::create([
            ...$request->validated(),
            'updated_by' => auth()->id(),
            'status' => 'draft'
        ]);
        
        return $this->sendResponse($dataValue, 'Məlumat uğurla əlavə edildi', 201);
    }

    public function show(DataValue $dataValue): JsonResponse
    {
        $dataValue->load(['school', 'column']);
        
        return $this->sendResponse($dataValue, 'Məlumat uğurla əldə edildi');
    }

    public function submit(DataValue $dataValue): JsonResponse
    {
        try {
            $dataValue->submit();
            return $this->sendResponse($dataValue, 'Məlumat uğurla göndərildi');
        } catch (\InvalidArgumentException $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    public function approve(DataValue $dataValue): JsonResponse
    {
        try {
            if (!auth()->user()->isSuperAdmin()) {
                return $this->sendError('Bu əməliyyat üçün icazəniz yoxdur', [], 403);
            }

            $dataValue->approve();
            return $this->sendResponse($dataValue, 'Məlumat uğurla təsdiqləndi');
        } catch (\InvalidArgumentException $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    public function reject(DataValue $dataValue): JsonResponse
    {
        try {
            if (!auth()->user()->isSuperAdmin()) {
                return $this->sendError('Bu əməliyyat üçün icazəniz yoxdur', [], 403);
            }

            $validated = request()->validate([
                'comment' => 'required|string'
            ]);

            $dataValue->reject($validated['comment']);
            return $this->sendResponse($dataValue, 'Məlumat uğurla rədd edildi');
        } catch (\InvalidArgumentException $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    public function bulkUpdate(BulkUpdateRequest $request): JsonResponse
    {
        try {
            \DB::beginTransaction();

            $updates = collect($request->updates)->map(function ($item) use ($request) {
                return DataValue::updateOrCreate(
                    [
                        'school_id' => $request->school_id,
                        'column_id' => $item['column_id']
                    ],
                    [
                        'value' => $item['value'],
                        'updated_by' => auth()->id(),
                        'status' => 'draft'
                    ]
                );
            });

            \DB::commit();

            return $this->sendResponse(
                $updates, 
                'Məlumatlar uğurla yeniləndi'
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return $this->sendError(
                'Məlumatları yeniləyərkən xəta baş verdi', 
                [$e->getMessage()], 
                500
            );
        }
    }
}