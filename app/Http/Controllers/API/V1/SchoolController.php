<?php

namespace App\Http\Controllers\API\V1;

use App\Application\DTOs\SchoolDTO;
use App\Application\Services\SchoolService;
use App\Http\Requests\API\V1\School\StoreSchoolRequest;
use App\Http\Requests\API\V1\School\UpdateSchoolRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SchoolController extends BaseController
{
    private SchoolService $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    /**
     * Display a listing of the schools.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $sectorId = $request->query('sector_id');
        $schools = $sectorId 
            ? $this->schoolService->getBySectorId($sectorId)
            : $this->schoolService->getAll();
            
        return $this->sendResponse($schools, 'Schools retrieved successfully');
    }

    /**
     * Store a newly created school in storage.
     *
     * @param StoreSchoolRequest $request
     * @return JsonResponse
     */
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        try {
            $dto = new SchoolDTO($request->validated());
            $school = $this->schoolService->create($dto);
            return $this->sendResponse($school, 'School created successfully', 201);
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error creating school', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified school.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $school = $this->schoolService->getById($id);
            return $this->sendResponse($school, 'School retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('School not found', [], 404);
        }
    }

    /**
     * Update the specified school in storage.
     *
     * @param UpdateSchoolRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSchoolRequest $request, int $id): JsonResponse
    {
        try {
            $dto = new SchoolDTO($request->validated());
            $school = $this->schoolService->update($id, $dto);
            return $this->sendResponse($school, 'School updated successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error updating school', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified school from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->schoolService->delete($id);
            return $this->sendResponse(null, 'School deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error deleting school', [$e->getMessage()], 500);
        }
    }

    /**
     * Get all admins for a specific school.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function admins(int $id): JsonResponse
    {
        try {
            $admins = $this->schoolService->getSchoolAdmins($id);
            return $this->sendResponse($admins, 'School admins retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving school admins', [$e->getMessage()], 500);
        }
    }
    public function search(Request $request)
    {
        $query = $request->get('query');
    
        return School::where('name', 'like', "%{$query}%")
            ->with(['admin' => function($q) {
                $q->select('id', 'school_id', 'username');
            }])
            ->limit(5)
            ->get(['id', 'name'])
            ->map(function($school) {
                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'admin_username' => $school->admin->username ?? null
                ];
            });
    }
}
