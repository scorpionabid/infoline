<?php

namespace App\Http\Controllers\API\V1;

use App\Application\DTOs\UserDTO;
use App\Application\Services\UserService;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Http\Requests\API\V1\Auth\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use App\Domain\Entities\User;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->sendError('Invalid credentials', [], 401);
            }

            if (Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid credentials', [], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token
            ], 'User logged in successfully');
        } catch (\Exception $e) {   
        \Log::error('Login error', ['error' => $e->getMessage()]);
        return $this->sendError('Error during login', [$e->getMessage()], 500);
        }
    }
    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = new UserDTO($request->validated());
            $user = $this->userService->create($dto);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (InvalidArgumentException $e) {
            return $this->sendError('Validation Error', [$e->getMessage()], 422);
        } catch (\Exception $e) {
            return $this->sendError('Error during registration', [$e->getMessage()], 500);
        }
    }

    /**
     * Handle user logout.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $request = request();
            $request->user()->currentAccessToken()->delete();
            
            return $this->sendResponse(null, 'User logged out successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error during logout', [$e->getMessage()], 500);
        }
    }

    /**
     * Get authenticated user details.
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        try {
            $user = request()->user();
            if (!$user) {
                return $this->sendError('Unauthorized', [], 401);
            }
            return $this->sendResponse($user, 'User details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving user details', [$e->getMessage()], 500);
        }
    }
}
