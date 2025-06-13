<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\ClientDTO;
use App\Models\Budget;
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
 * @OA\Info(
 *     title="Budget API",
 *     version="1.0.0",
 *     description="API endpoints for managing budgets"
 * )

 * @OA\Tag(
 *      name="Budgets",
 *      description="API Endpoints for Budget operations"
 *  )
 * @OA\Schema(
 *     schema="Budget",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="made_date", type="string", format="date"),
 *     @OA\Property(property="dead_line", type="string", format="date"),
 *     @OA\Property(property="profit", type="number", format="float"),
 *     @OA\Property(property="total_price", type="number", format="float"),
 *     @OA\Property(property="client_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="ApiResponse",
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status", type="integer")
 * )
* @OA\Schema(
 *     schema="BudgetRequest",
 *     required={"description","made_date","client_id"},
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="made_date", type="string", format="date"),
 *     @OA\Property(property="dead_line", type="string", format="date", nullable=true),
 *     @OA\Property(property="profit", type="number", format="float", nullable=true),
 *     @OA\Property(property="client_id", type="integer")
 * )
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
     * @OA\Get(
     *     path="/api/v1/budgets",
     *     summary="Get all budgets",
     *     tags={"Budgets"},
     *     @OA\Response(
     *         response=200,
     *         description="List of budgets retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Budget")
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/v1/budgets/{id}",
     *     summary="Get budget by ID",
     *     tags={"Budgets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Budget ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Budget found",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Budget not found"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/v1/budgets",
     *     summary="Create a new budget",
     *     tags={"Budgets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description","made_date","client_id"},
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="made_date", type="string", format="date"),
     *             @OA\Property(property="dead_line", type="string", format="date"),
     *             @OA\Property(property="profit", type="number"),
     *             @OA\Property(property="client_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Budget created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/v1/budgets/{id}",
     *     summary="Update an existing budget",
     *     tags={"Budgets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BudgetRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Budget updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/v1/budgets/{id}",
     *     summary="Delete a budget",
     *     tags={"Budgets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Budget deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Budget not found"
     *     )
     * )
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

    /**
     * @OA\Get (
     *     path="/api/v1/budgets/updatePrice/{id}",
     *     summary="Update budget price",
     *     tags={"Budgets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Budget price updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     )
     * )
     */
    public function updateBudgetPrice(int $id)
    {
        $budget = $this->service->get($id);
        $budget->updatePrice();
        return $this->successResponse($budget,
            "Budget price updated successfully",

        );
    }

    /**
     * @OA\Post (
     *     path="/api/v1/budgets/states/{id}/{state}",
     *     summary="Change budget state",
     *     tags={"Budgets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Work ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="path",
     *         required=true,
     *         description="New state",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="State changed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Budget")
     *     )
     * )
     */
    public function changeState(int $id, string $state): JsonResponse
    {
        try {
            $work = $this->service->changeState($id, $state);
            return $this->successResponse(new BudgetResource($work), "State changed successfully", Response::HTTP_OK);
        }catch (Exception $e) {
            return $this->errorResponse(
                "Controller Error: changing state of budget",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getStates() : JsonResponse
    {
        try {
            return  $this->successResponse(Budget::getStates(), "States retrieved successfully", Response::HTTP_OK);

        }catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function getPaginate()
    {
        $results = $this->service->paginate();
        if ($results instanceof JsonResponse) {
            return $results;
        }
        return $this->successResponse(
            new BudgetResourceCollection($results),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

}
