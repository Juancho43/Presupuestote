<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\StockRepository;
use App\DTOs\V1\StockDTO;
use App\Models\Stock;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StockService
 *
 * Service layer for handling business logic related to Stock entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class StockService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var StockService|null
     */
    private static ?StockService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var StockRepository
     */
    private StockRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return StockService
     */
    public static function getInstance(): StockService
    {
        if (self::$instance === null) {
            self::$instance = new self(new StockRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param StockRepository $repository Repository for data operations
     */
    public function __construct(StockRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Stock entity by ID
     *
     * @param int $id The entity ID
     * @return Stock|JsonResponse The found entity or error response
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
                "Service Error: can't find Stock",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Stock entities
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
     * Create a new Stock entity
     *
     * @param StockDTO $data Data transfer object containing entity information
     * @return Stock|JsonResponse The created entity or error response
     */
    public function create(StockDTO $data): Model|JsonResponse
    {
        try {
            $newStock = $this->repository->create($data);
            return $newStock;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Stock",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Stock entity
     *
     * @param StockDTO $data Data transfer object containing updated information
     * @return Stock|JsonResponse The updated entity or error response
     */
    public function update(StockDTO $data): Model|JsonResponse
    {
        try {
            $updatedStock = $this->repository->update($data);
            return $updatedStock;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Stock",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Stock entity by ID
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
                "Service Error: can't delete Stock",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
