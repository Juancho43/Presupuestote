<?php
namespace App\Services\V1;

use App\DTOs\V1\ClientDTO;
use App\DTOs\V1\PersonDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\EmployeeRepository;
use App\DTOs\V1\EmployeeDTO;
use App\Models\Employee;
use App\Repository\V1\PersonRepository;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeService
 *
 * Service layer for handling business logic related to Employee entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class EmployeeService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var EmployeeService|null
     */
    private static ?EmployeeService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var EmployeeRepository
     */
    private EmployeeRepository $repository;

    private PersonRepository $personRepository;
    /**
     * Get or create the singleton instance
     *
     * @return EmployeeService
     */
    public static function getInstance(): EmployeeService
    {
        if (self::$instance === null) {
            self::$instance = new self(new EmployeeRepository(), new PersonRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param EmployeeRepository $repository Repository for data operations
     */
    public function __construct(EmployeeRepository $repository, PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Employee entity by ID
     *
     * @param int $id The entity ID
     * @return Employee|JsonResponse The found entity or error response
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
                "Service Error: can't find Employee",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Employee entities
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
     * Create a new Employee entity
     *
     * @param EmployeeDTO $data Data transfer object containing entity information
     * @return Employee|JsonResponse The created entity or error response
     */
    public function create(EmployeeDTO $data): Model|JsonResponse
    {
        try {

            $personId = $data->person->id;
            if ($data->person->id == null) {
                $person = $this->personRepository->create($data->person);
                $personId = $person->id;
            }

            $newEmployee = $this->repository->create(new EmployeeDTO(
                null,
                $data->salary,
                $data->startDate,
                $data->endDate,
                $data->isActive,
                new PersonDTO(id:$personId)
            ));
            return $newEmployee;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create client",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Employee entity
     *
     * @param EmployeeDTO $data Data transfer object containing updated information
     * @return Employee|JsonResponse The updated entity or error response
     */
    public function update(EmployeeDTO $data): Model|JsonResponse
    {
        try {
            $this->repository->update($data);
            $this->personRepository->update($data->person);
            return $this->get($data->id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Employee",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Employee entity by ID
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
                "Service Error: can't delete Employee",
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
