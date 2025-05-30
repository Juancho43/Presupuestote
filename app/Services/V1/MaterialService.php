<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\MaterialRepository;
use App\DTOs\V1\MaterialDTO;
use App\Models\Material;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MaterialService
 *
 * Service layer for handling business logic related to Material entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class MaterialService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var MaterialService|null
     */
    private static ?MaterialService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var MaterialRepository
     */
    private MaterialRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return MaterialService
     */
    public static function getInstance(): MaterialService
    {
        if (self::$instance === null) {
            self::$instance = new self(new MaterialRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param MaterialRepository $repository Repository for data operations
     */
    public function __construct(MaterialRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Material entity by ID
     *
     * @param int $id The entity ID
     * @return Material|JsonResponse The found entity or error response
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
                "Service Error: can't find Material",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Material entities
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
     * Create a new Material entity
     *
     * @param MaterialDTO $data Data transfer object containing entity information
     * @return Material|JsonResponse The created entity or error response
     */
    public function create(MaterialDTO $data): Model|JsonResponse
    {
        try {
            $newMaterial = $this->repository->create($data);
            return $newMaterial;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Material",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Material entity
     *
     * @param MaterialDTO $data Data transfer object containing updated information
     * @return Material|JsonResponse The updated entity or error response
     */
    public function update(MaterialDTO $data): Model|JsonResponse
    {
        try {
            $updatedMaterial = $this->repository->update($data);
            return $updatedMaterial;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Material",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Material entity by ID
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
                "Service Error: can't delete Material",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    public function getWithInvoices(int $id): Material|JsonResponse
    {
        return $this->repository->getWithInvoices($id);
    }

    public function getWithWorks(int $id): Material|JsonResponse
    {
        return $this->repository->getWithWorks($id);
    }

    public function getWithStocks(int $id): Material|JsonResponse
    {
        return $this->repository->getWithStocks($id);
    }

    public function getWithPrices(int $id): Material|JsonResponse
    {
        return $this->repository->getWithPrices($id);
    }
}
