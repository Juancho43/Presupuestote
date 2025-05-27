<?php
namespace App\Services\V1;

use App\DTOs\V1\ClientDTO;
use App\DTOs\V1\PersonDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\PersonRepository;
use App\Repository\V1\SupplierRepository;
use App\DTOs\V1\SupplierDTO;
use App\Models\Supplier;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SupplierService
 *
 * Service layer for handling business logic related to Supplier entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class SupplierService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var SupplierService|null
     */
    private static ?SupplierService $instance = null;
    private PersonRepository $personRepository;
    /**
     * Repository for data access operations
     *
     * @var SupplierRepository
     */
    private SupplierRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return SupplierService
     */
    public static function getInstance(): SupplierService
    {
        if (self::$instance === null) {
            self::$instance = new self(new SupplierRepository(), new PersonRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param SupplierRepository $repository Repository for data operations
     */
    public function __construct(SupplierRepository $repository, PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Supplier entity by ID
     *
     * @param int $id The entity ID
     * @return Supplier|JsonResponse The found entity or error response
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
                "Service Error: can't find Supplier",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Supplier entities
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
     * Create a new Supplier entity
     *
     * @param SupplierDTO $data Data transfer object containing entity information
     * @return Supplier|JsonResponse The created entity or error response
     */
    public function create(SupplierDTO $data): Model|JsonResponse
    {
        try {

            $personId = $data->person->id;
            if ($data->person->id == null) {
                $person = $this->personRepository->create($data->person);
                $personId = $person->id;
            }

            $newClient = $this->repository->create(new SupplierDTO(
                null,
                $data->notes,
                $data->balance,
                new PersonDTO(id:$personId)
            ));
            return $newClient;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create client",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Supplier entity
     *
     * @param SupplierDTO $data Data transfer object containing updated information
     * @return Supplier|JsonResponse The updated entity or error response
     */
    public function update(SupplierDTO $data): Model|JsonResponse
    {
        try {
            $this->personRepository->update($data->person);
            return $this->get($data->id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Supplier",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Supplier entity by ID
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
                "Service Error: can't delete Supplier",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    public function updateBalance(int $id): void
    {
        $this->get($id)->updateBalance();
    }
}
