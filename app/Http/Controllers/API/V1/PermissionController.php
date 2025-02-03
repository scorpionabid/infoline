<?php

namespace App\Http\Controllers\API\V1;

use App\Domain\Entities\Permission;
use App\Http\Controllers\API\V1\BaseController;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends BaseController
{
    public function index(): JsonResponse
    {
        if (!auth()->user()->isSuperAdmin()) {
            return $this->sendError('Bu əməliyyat üçün icazəniz yoxdur', [], 403);
        }
        $permissions = Permission::all();
    
        return $this->sendResponse($permissions, 'İcazələr uğurla əldə edildi');
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = Permission::create($request->validated());
        
        return $this->sendResponse($permission, 'İcazə uğurla yaradıldı', 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return $this->sendResponse($permission, 'İcazə uğurla əldə edildi');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission->update($request->validated());
        
        return $this->sendResponse($permission, 'İcazə uğurla yeniləndi');
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();
        
        return $this->sendResponse(null, 'İcazə uğurla silindi');
    }

    public function assignRole(Permission $permission): JsonResponse
    {
        $validated = request()->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $permission->roles()->syncWithoutDetaching($validated['role_id']);
        
        return $this->sendResponse(null, 'İcazə rola uğurla əlavə edildi');
    }
}