<?php
namespace App\Services\V1;

use App\DTOs\V1\PersonDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Person;
use App\Repository\V1\PersonRepository;
use App\Repository\V1\PriceRepository;
use App\DTOs\V1\PriceDTO;
use App\Models\Price;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PriceService
 *
 * Service layer for handling business logic related to Person entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class PersonService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var PersonService|null
     */
    private static ?PersonService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var PersonRepository
     */
    private PersonRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return PersonRepository
     */
    public static function getInstance(): PersonService
    {
        if (self::$instance === null) {
            self::$instance = new self(new PersonRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param PersonRepository $repository Repository for data operations
     */
    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Person entity by ID
     *
     * @param int $id The entity ID
     * @return Person|JsonResponse The found entity or error response
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
                "Service Error: can't find Person",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Person entities
     *
     * @return Collection|JsonResponse Collection of entities or error response
     */
    public function getAll(): Collection|JsonResponse
    {
        try {
            return $this->repository->all();
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve People",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create a new Person entity
     *
     * @param PersonDTO $data Data transfer object containing entity information
     * @return Person|JsonResponse The created entity or error response
     */
    public function create(PersonDTO $data): Model|JsonResponse
    {
        try {
            $newPerson = $this->repository->create($data);
            return $newPerson;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Person",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Person entity
     *
     * @param PersonDTO $data Data transfer object containing updated information
     * @return Person|JsonResponse The updated entity or error response
     */
    public function update(PersonDTO $data): Model|JsonResponse
    {
        try {
            $updatedPerson = $this->repository->update($data);
            return $updatedPerson;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Person",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Person entity by ID
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
                "Service Error: can't delete Person",
                $e->getMessage(),
                $statusCode
            );
        }
    }
}
