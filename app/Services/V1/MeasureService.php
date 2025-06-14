<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\MeasureRepository;
use App\DTOs\V1\MeasureDTO;
use App\Models\Measure;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeasureService
 *
 * Service layer for handling business logic related to Measure entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class MeasureService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var MeasureService|null
     */
    private static ?MeasureService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var MeasureRepository
     */
    private MeasureRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return MeasureService
     */
    public static function getInstance(): MeasureService
    {
        if (self::$instance === null) {
            self::$instance = new self(new MeasureRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param MeasureRepository $repository Repository for data operations
     */
    public function __construct(MeasureRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Measure entity by ID
     *
     * @param int $id The entity ID
     * @return Measure|JsonResponse The found entity or error response
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
                "Service Error: can't find Measure",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Measure entities
     *
     * @return Paginator|JsonResponse Collection of entities or error response
     */
    public function getAll(int $page): Paginator|JsonResponse
    {
        try {
            return $this->repository->all($page);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve dummies",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create a new Measure entity
     *
     * @param MeasureDTO $data Data transfer object containing entity information
     * @return Measure|JsonResponse The created entity or error response
     */
    public function create(MeasureDTO $data): Model|JsonResponse
    {
        try {
            $newMeasure = $this->repository->create($data);
            return $newMeasure;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Measure",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Measure entity
     *
     * @param MeasureDTO $data Data transfer object containing updated information
     * @return Measure|JsonResponse The updated entity or error response
     */
    public function update(MeasureDTO $data): Model|JsonResponse
    {
        try {
            $updatedMeasure = $this->repository->update($data);
            return $updatedMeasure;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Measure",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Measure entity by ID
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
                "Service Error: can't delete Measure",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    public function search(string $query): Collection|JsonResponse
    {
        try {
            return $this->repository->search($query);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't search Measures",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
