<?php
namespace App\Services\V1;

use App\DTOs\V1\PaymentDTO;
use App\DTOs\V1\PersonDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\ClientRepository;
use App\DTOs\V1\ClientDTO;
use App\Models\Client;
use App\Repository\V1\PersonRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClientService
 *
 * Service layer for handling business logic related to Client entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class ClientService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var ClientService|null
     */
    private static ?ClientService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var ClientRepository
     */
    private ClientRepository $repository;


    private PersonRepository $personRepository;
    /**
     * Get or create the singleton instance
     *
     * @return ClientService
     */
    public static function getInstance(): ClientService
    {
        if (self::$instance === null) {
            self::$instance = new self(new ClientRepository(), new PersonRepository());
        }
        return self::$instance;
    }



    /**
     * Constructor
     *
     * @param ClientRepository $repository Repository for data operations
     */
    public function __construct(ClientRepository $repository, PersonRepository $personRepository)
    {
        $this->repository = $repository;
        $this->personRepository = $personRepository;
    }


    /**
     * Retrieve a specific Client entity by ID
     *
     * @param int $id The entity ID
     * @return Client|JsonResponse The found entity or error response
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
                "Service Error: can't find client",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Client entities
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
     * Create a new Client entity
     *
     * @param ClientDTO $data Data transfer object containing entity information
     * @return Client|JsonResponse The created entity or error response
     */
    public function create(ClientDTO $data): Model|JsonResponse
    {
        try {

            $personId = $data->person->id;
            if ($data->person->id == null) {
                $person = $this->personRepository->create($data->person);
                $personId = $person->id;
            }

            $newClient = $this->repository->create(new ClientDTO(null,
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
     * Update an existing Client entity
     *
     * @param ClientDTO $data Data transfer object containing updated information
     * @return Client|JsonResponse The updated entity or error response
     */
    public function update(ClientDTO $data): Model|JsonResponse
    {
        try {
            $updatedClient = $this->repository->update($data);
            return $updatedClient;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update client",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Client entity by ID
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
                "Service Error: can't delete client",
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
