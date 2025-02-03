<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\ImportDataRequest;
use App\Services\Excel\DataImport;
use App\Domain\Entities\School;
use Illuminate\Http\JsonResponse;

class ImportController extends BaseController
{
    private DataImport $importer;
    
    public function __construct(DataImport $importer)
    {
        $this->importer = $importer;
    }
    
    public function import(ImportDataRequest $request): JsonResponse
    {
        try {
            $school = School::findOrFail($request->school_id);
            $result = $this->importer->import($request->file('file'), $school);
            
            $resultArray = $result->toArray();
            
            if (!$resultArray['success']) {
                return $this->sendError(
                    'İdxal zamanı xəta baş verdi',
                    $resultArray['errors']
                );
            }
            
            return $this->sendResponse(
                $resultArray,
                sprintf(
                    '%d məlumat uğurla idxal edildi',
                    $resultArray['imported_count']
                )
            );
            
        } catch (\Exception $e) {
            return $this->sendError(
                'İdxal zamanı xəta baş verdi',
                [$e->getMessage()]
            );
        }
    }
}