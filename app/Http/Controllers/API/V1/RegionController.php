<?php

namespace App\Http\Controllers\API\V1;

use App\Application\DTOs\RegionDTO;
use App\Application\Services\RegionService;
use App\Http\Requests\API\V1\Region\StoreRegionRequest;
use App\Http\Requests\API\V1\Region\UpdateRegionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RegionController extends BaseController
{
    private RegionService $regionService;

    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    /**
     * Display a listing of the regions.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $regions = $this->regionService->getAll();
        return $this->sendResponse($regions, 'Regions retrieved successfully');
    }

    /**
     * Store a newly created region in storage.
     *
     * @param StoreRegionRequest $request
     * @return JsonResponse
     */
    public function store(StoreRegionRequest $request): JsonResponse
    {
        try {
            $dto = new RegionDTO($request->validated());
            $region = $this->regionService->create($dto);
            return $this->sendResponse($region, 'Region created successfully', 201);
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error creating region', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified region.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $region = $this->regionService->getById($id);
            return $this->sendResponse($region, 'Region retrieved successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Region not found', [], 404);
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving region', [$e->getMessage()], 500);
        }
    }

    /**
     * Update the specified region in storage.
     *
     * @param UpdateRegionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRegionRequest $request, int $id): JsonResponse
    {
        try {
            $dto = new RegionDTO($request->validated());
            $region = $this->regionService->update($id, $dto);
            return $this->sendResponse($region, 'Region updated successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error updating region', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified region from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->regionService->delete($id);
            return $this->sendResponse(null, 'Region deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error deleting region', [$e->getMessage()], 500);
        }
    }
}
