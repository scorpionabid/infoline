<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\Role;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\API\V1\Role\StoreRoleRequest;
use App\Http\Requests\API\V1\Role\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                return $this->sendError('Bu əməliyyat üçün icazəniz yoxdur', [], 403);
            }
            return $next($request);
        });
    }

    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();
        
        return $this->sendResponse($roles, 'Rollar uğurla əldə edildi');
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $roleData = $request->validated();
        $roleData['is_system'] = false; // default olaraq sistem rolu deyil
        
        $role = Role::create($roleData);
        $role->load('permissions');
        
        return $this->sendResponse($role, 'Rol uğurla yaradıldı', 201);
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return $this->sendResponse($role, 'Rol uğurla əldə edildi');
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        if ($role->isSystem()) {
            return $this->sendError('Sistem rolları dəyişdirilə bilməz', [], 422);
        }

        $role->update($request->validated());
        $role->load('permissions');
        
        return $this->sendResponse($role, 'Rol uğurla yeniləndi');
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->isSystem()) {
            return $this->sendError('Sistem rolları silinə bilməz', [], 422);
        }

        $role->delete();
        
        return $this->sendResponse(null, 'Rol uğurla silindi');
    }
}