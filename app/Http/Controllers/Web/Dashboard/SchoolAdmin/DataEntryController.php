<?php
// app/Http/Controllers/Web/Dashboard/SchoolAdmin/DataEntryController.php

namespace App\Http\Controllers\Web\Dashboard\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolAdmin\DataEntryRequest;
use App\Http\Requests\SchoolAdmin\BulkUpdateRequest;
use App\Services\Dashboard\SchoolAdmin\DataEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DataEntryController extends Controller
{
    protected $dataEntryService;

    public function __construct(DataEntryService $dataEntryService)
    {
        $this->dataEntryService = $dataEntryService;
    }

    /**
     * Tək sütun üçün məlumat saxlama
     */
    public function store(DataEntryRequest $request): JsonResponse
    {
        try {
            $result = $this->dataEntryService->saveColumnValue(
                Auth::user()->school_id,
                $request->column_id,
                $request->value
            );

            return response()->json([
                'success' => true,
                'message' => 'Məlumat uğurla yadda saxlanıldı',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Çoxlu məlumat yeniləmə
     */
    public function bulkUpdate(BulkUpdateRequest $request): JsonResponse
    {
        try {
            $result = $this->dataEntryService->bulkUpdateValues(
                Auth::user()->school_id,
                $request->values
            );

            return response()->json([
                'success' => true,
                'message' => 'Məlumatlar uğurla yeniləndi',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }
}