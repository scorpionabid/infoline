<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\DataValue;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\API\V1\DataValue\StoreDataValueRequest;
use Illuminate\Http\JsonResponse;

class DataValueController extends BaseController
{
    public function index(): JsonResponse
    {
        $dataValues = DataValue::with(['school', 'column'])
            ->latest()
            ->get();
        
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
}