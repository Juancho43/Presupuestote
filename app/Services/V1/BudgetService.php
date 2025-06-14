<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\BudgetRepository;
use App\DTOs\V1\BudgetDTO;
use App\Models\Budget;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BudgetService
 *
 * Service layer for handling business logic related to Budget entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class BudgetService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var BudgetService|null
     */
    private static ?BudgetService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var BudgetRepository
     */
    private BudgetRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return BudgetService
     */
    public static function getInstance(): BudgetService
    {
        if (self::$instance === null) {
            self::$instance = new self(new BudgetRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param BudgetRepository $repository Repository for data operations
     */
    public function __construct(BudgetRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Budget entity by ID
     *
     * @param int $id The entity ID
     * @return Budget|JsonResponse The found entity or error response
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
                "Service Error: can't find Budget",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Budget entities
     *
     * @return Collection|JsonResponse Collection of entities or error response
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
     * Create a new Budget entity
     *
     * @param BudgetDTO $data Data transfer object containing entity information
     * @return Budget|JsonResponse The created entity or error response
     */
    public function create(BudgetDTO $data): Budget|JsonResponse
    {
        try {
            $newBudget = $this->repository->create($data);
            $newBudget->updatePrice();
            $newBudget->fresh();
            return $newBudget;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Budget",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Budget entity
     *
     * @param BudgetDTO $data Data transfer object containing updated information
     * @return Budget|JsonResponse The updated entity or error response
     */
    public function update(BudgetDTO $data): Model|JsonResponse
    {
        try {
            $updatedBudget = $this->repository->update($data);
            $updatedBudget->updatePrice();
            $updatedBudget->fresh();
            return $updatedBudget;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Budget",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Budget entity by ID
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
                "Service Error: can't delete Budget",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    public function changeState(int $id, string $stateName): Budget | JsonResponse
    {
        try {
            $budget = $this->repository->find($id);
            if($budget->state->canTransitionTo($stateName)) {
                $budget->state->transitionTo($stateName);
                $budget->save();
            }else{
                throw new Exception("State transition failed");
            }
            return $budget;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't change state",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
