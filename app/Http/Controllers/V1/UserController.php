<?php
namespace App\Http\Controllers\V1;

use App\Services\V1\UserService;
use App\DTOs\V1\UserDTO;
use App\Http\Requests\V1\UserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Controller
 *
 * Handles HTTP requests related to user records including CRUD operations
 */
class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var UserService Service for user data logic
     */
    protected UserService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all user records
     *
     * @return JsonResponse Collection of user records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new UserResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single user record by ID
     *
     * @param int $id User record ID
     * @return JsonResponse Single user resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new UserResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new user record
     *
     * @param UserRequest $request Validated User data
     * @return JsonResponse Created user resource
     */
    public function store(UserRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $userDTO = new UserDTO($request->validated());

    $result = $this->service->create($userDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new UserResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing user record
     *
     * @param UserRequest $request Validated User data
     * @return JsonResponse Updated user resource
     */
    public function update(int $id,UserRequest $request): JsonResponse
    {
        $userDTO = new UserDTO($id);
        $result = $this->service->update($userDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new UserResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete user record
     *
     * @param int $id User record ID
     * @return JsonResponse Empty response on success
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->service->delete($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            "Data deleted successfully",
            Response::HTTP_NO_CONTENT
        );
    }
}
