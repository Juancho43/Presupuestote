<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\SalaryRepository;
use App\DTOs\V1\SalaryDTO;
use App\Models\Salary;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SalaryService
 *
 * Service layer for handling business logic related to Salary entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class SalaryService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var SalaryService|null
     */
    private static ?SalaryService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var SalaryRepository
     */
    private SalaryRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return SalaryService
     */
    public static function getInstance(): SalaryService
    {
        if (self::$instance === null) {
            self::$instance = new self(new SalaryRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param SalaryRepository $repository Repository for data operations
     */
    public function __construct(SalaryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Salary entity by ID
     *
     * @param int $id The entity ID
     * @return Salary|JsonResponse The found entity or error response
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
                "Service Error: can't find Salary",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Salary entities
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
     * Create a new Salary entity
     *
     * @param SalaryDTO $data Data transfer object containing entity information
     * @return Salary|JsonResponse The created entity or error response
     */
    public function create(SalaryDTO $data): Model|JsonResponse
    {
        try {
            $newSalary = $this->repository->create($data);
            return $newSalary;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Salary",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Salary entity
     *
     * @param SalaryDTO $data Data transfer object containing updated information
     * @return Salary|JsonResponse The updated entity or error response
     */
    public function update(SalaryDTO $data): Model|JsonResponse
    {
        try {
            $updatedSalary = $this->repository->update($data);
            return $updatedSalary;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Salary",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Salary entity by ID
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
                "Service Error: can't delete Salary",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
