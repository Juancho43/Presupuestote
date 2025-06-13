<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\SubCategoryRepository;
use App\DTOs\V1\SubCategoryDTO;
use App\Models\SubCategory;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SubCategoryService
 *
 * Service layer for handling business logic related to SubCategory entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class SubCategoryService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var SubCategoryService|null
     */
    private static ?SubCategoryService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var SubCategoryRepository
     */
    private SubCategoryRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return SubCategoryService
     */
    public static function getInstance(): SubCategoryService
    {
        if (self::$instance === null) {
            self::$instance = new self(new SubCategoryRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param SubCategoryRepository $repository Repository for data operations
     */
    public function __construct(SubCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific SubCategory entity by ID
     *
     * @param int $id The entity ID
     * @return SubCategory|JsonResponse The found entity or error response
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
                "Service Error: can't find SubCategory",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all SubCategory entities
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
     * Create a new SubCategory entity
     *
     * @param SubCategoryDTO $data Data transfer object containing entity information
     * @return SubCategory|JsonResponse The created entity or error response
     */
    public function create(SubCategoryDTO $data): Model|JsonResponse
    {
        try {
            $newSubCategory = $this->repository->create($data);
            return $newSubCategory;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create SubCategory",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing SubCategory entity
     *
     * @param SubCategoryDTO $data Data transfer object containing updated information
     * @return SubCategory|JsonResponse The updated entity or error response
     */
    public function update(SubCategoryDTO $data): Model|JsonResponse
    {
        try {
            $updatedSubCategory = $this->repository->update($data);
            return $updatedSubCategory;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update SubCategory",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a SubCategory entity by ID
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
                "Service Error: can't delete SubCategory",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
