<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\ClientDTO;
use App\Services\V1\BudgetService;
use App\DTOs\V1\BudgetDTO;
use App\Http\Requests\V1\BudgetRequest;
use App\Http\Resources\V1\BudgetResource;
use App\Http\Resources\V1\BudgetResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Budget Controller
 *
 * Handles HTTP requests related to budget records including CRUD operations
 */
class BudgetController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var BudgetService Service for budget data logic
     */
    protected BudgetService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param BudgetService $service
     */
    public function __construct(BudgetService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all budget records
     *
     * @return JsonResponse Collection of budget records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new BudgetResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single budget record by ID
     *
     * @param int $id Budget record ID
     * @return JsonResponse Single budget resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new BudgetResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new budget record
     *
     * @param BudgetRequest $request Validated budget data
     * @return JsonResponse Created budget resource
     */
    public function store(BudgetRequest $request): JsonResponse
    {
        try {
            // Parse dates
            try {
                $madeDate = Carbon::parse($request->made_date);
                $deadLine = isset($request->dead_line) ? Carbon::parse($request->dead_line) : null;
            } catch (Exception $e) {
                return $this->errorResponse(
                    "Invalid date format",
                    $e->getMessage(),
                    Response::HTTP_BAD_REQUEST
                );
            }
            try {
                $budgetDTO = new BudgetDTO(
                    null,
                    $request->description,
                    $madeDate,
                    $deadLine,
                     null,
                    isset($request->profit) ? $request->profit : null,
                    null,
                    null,
                    null,
                    new ClientDTO(id: $request->client_id),
                );
            } catch (Exception $e) {
                return $this->errorResponse(
                    "Error creating BudgetDTO",
                    $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            // Call service to create budget
            $result = $this->service->create($budgetDTO);

            if ($result instanceof JsonResponse) {
                return $result;
            }

            return $this->successResponse(
                new BudgetResource($result),
                "Data stored successfully",
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                "An unexpected error occurred",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    /**
     * Update existing budget record
     *
     * @param BudgetRequest $request Validated budget data
     * @return JsonResponse Updated budget resource
     */
    public function update(int $id, BudgetRequest $request): JsonResponse
    {
        $budgetDTO = new BudgetDTO(
            $id,
            $request->description,
            new Carbon($request->made_date),
            new Carbon($request->dead_line),
            null,
            $request->profit,
            null,
            null,
            null,
            new ClientDTO(id: $request->client_id),
        );
        $result = $this->service->update($budgetDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new BudgetResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete budget record
     *
     * @param int $id Budget record ID
     * @return JsonResponse Empty response on success
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->service->delete($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            "Data deleted successfully",
            Response::HTTP_NO_CONTENT
        );
    }
    public function updateBudgetPrice(int $id)
    {
        $budget = $this->service->get($id);
        $budget->updatePrice();
        return $this->successResponse($budget,
            "Budget price updated successfully",

        );
    }
}
