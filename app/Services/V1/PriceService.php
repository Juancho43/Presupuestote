<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\PriceRepository;
use App\DTOs\V1\PriceDTO;
use App\Models\Price;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PriceService
 *
 * Service layer for handling business logic related to Price entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class PriceService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var PriceService|null
     */
    private static ?PriceService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var PriceRepository
     */
    private PriceRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return PriceService
     */
    public static function getInstance(): PriceService
    {
        if (self::$instance === null) {
            self::$instance = new self(new PriceRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param PriceRepository $repository Repository for data operations
     */
    public function __construct(PriceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Price entity by ID
     *
     * @param int $id The entity ID
     * @return Price|JsonResponse The found entity or error response
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
                "Service Error: can't find Price",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Price entities
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
     * Create a new Price entity
     *
     * @param PriceDTO $data Data transfer object containing entity information
     * @return Price|JsonResponse The created entity or error response
     */
    public function create(PriceDTO $data): Model|JsonResponse
    {
        try {
            $newPrice = $this->repository->create($data);
            return $newPrice;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Price",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Price entity
     *
     * @param PriceDTO $data Data transfer object containing updated information
     * @return Price|JsonResponse The updated entity or error response
     */
    public function update(PriceDTO $data): Model|JsonResponse
    {
        try {
            $updatedPrice = $this->repository->update($data);
            return $updatedPrice;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Price",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Price entity by ID
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
                "Service Error: can't delete Price",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
