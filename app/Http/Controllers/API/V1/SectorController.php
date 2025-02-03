<?php

namespace App\Http\Controllers\API\V1;

use App\Application\DTOs\SectorDTO;
use App\Application\Services\SectorService;
use App\Http\Requests\API\V1\Sector\StoreSectorRequest;
use App\Http\Requests\API\V1\Sector\UpdateSectorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SectorController extends BaseController
{
    private SectorService $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    /**
     * Display a listing of the sectors.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $regionId = $request->query('region_id');
        $sectors = $regionId 
            ? $this->sectorService->getByRegionId($regionId)
            : $this->sectorService->getAll();
            
        return $this->sendResponse($sectors, 'Sectors retrieved successfully');
    }

    /**
     * Store a newly created sector in storage.
     *
     * @param StoreSectorRequest $request
     * @return JsonResponse
     */
    public function store(StoreSectorRequest $request): JsonResponse
    {
        try {
            $dto = new SectorDTO($request->validated());
            $sector = $this->sectorService->create($dto);
            return $this->sendResponse($sector, 'Sector created successfully', 201);
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error creating sector', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified sector.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $sector = $this->sectorService->getById($id);
            return $this->sendResponse($sector, 'Sector retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Sector not found', [], 404);
        }
    }

    /**
     * Update the specified sector in storage.
     *
     * @param UpdateSectorRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSectorRequest $request, int $id): JsonResponse
    {
        try {
            $dto = new SectorDTO($request->validated());
            $sector = $this->sectorService->update($id, $dto);
            return $this->sendResponse($sector, 'Sector updated successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error updating sector', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified sector from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->sectorService->delete($id);
            return $this->sendResponse(null, 'Sector deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error deleting sector', [$e->getMessage()], 500);
        }
    }
}
