<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\CategoryRepository;
use App\DTOs\V1\CategoryDTO;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryService
 *
 * Service layer for handling business logic related to Category entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class CategoryService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var CategoryService|null
     */
    private static ?CategoryService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var CategoryRepository
     */
    private CategoryRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return CategoryService
     */
    public static function getInstance(): CategoryService
    {
        if (self::$instance === null) {
            self::$instance = new self(new CategoryRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param CategoryRepository $repository Repository for data operations
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Category entity by ID
     *
     * @param int $id The entity ID
     * @return Category|JsonResponse The found entity or error response
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
                "Service Error: can't find Category",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Category entities
     *
     * @return Paginator|JsonResponse Collection of entities or error response
     */
    public function getAll(): Paginator|JsonResponse
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
     * Create a new Category entity
     *
     * @param CategoryDTO $data Data transfer object containing entity information
     * @return Category|JsonResponse The created entity or error response
     */
    public function create(CategoryDTO $data): Model|JsonResponse
    {
        try {
            $newCategory = $this->repository->create($data);
            return $newCategory;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Category",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Category entity
     *
     * @param CategoryDTO $data Data transfer object containing updated information
     * @return Category|JsonResponse The updated entity or error response
     */
    public function update(CategoryDTO $data): Model|JsonResponse
    {
        try {
            $updatedCategory = $this->repository->update($data);
            return $updatedCategory;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Category",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Category entity by ID
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
                "Service Error: can't delete Category",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
