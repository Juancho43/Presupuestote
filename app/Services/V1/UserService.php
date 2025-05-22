<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\UserRepository;
use App\DTOs\V1\UserDTO;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserService
 *
 * Service layer for handling business logic related to User entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class UserService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var UserService|null
     */
    private static ?UserService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return UserService
     */
    public static function getInstance(): UserService
    {
        if (self::$instance === null) {
            self::$instance = new self(new UserRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param UserRepository $repository Repository for data operations
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific User entity by ID
     *
     * @param int $id The entity ID
     * @return User|JsonResponse The found entity or error response
     */
    public function get(int $id): Model|JsonResponse
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't find User",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all User entities
     *
     * @return Collection|JsonResponse Collection of entities or error response
     */
    public function getAll(): Collection|JsonResponse
    {
        try {
            return $this->repository->all();
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve dummies",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create a new User entity
     *
     * @param UserDTO $data Data transfer object containing entity information
     * @return User|JsonResponse The created entity or error response
     */
    public function create(UserDTO $data): Model|JsonResponse
    {
        try {
            $newUser = $this->repository->create($data);
            return $newUser;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create User",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing User entity
     *
     * @param UserDTO $data Data transfer object containing updated information
     * @return User|JsonResponse The updated entity or error response
     */
    public function update(UserDTO $data): Model|JsonResponse
    {
        try {
            $updatedUser = $this->repository->update($data);
            return $updatedUser;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update User",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a User entity by ID
     *
     * @param int $id The entity ID
     * @return bool|JsonResponse True if successful or error response
     */
    public function delete(int $id): bool|JsonResponse
    {
        try {
            return $this->repository->delete($id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't delete User",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
