<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\BudgetDTO;
use App\Http\Requests\V1\AddMaterialsToWorksRequest;
use App\Models\Work;
use App\Services\V1\WorkService;
use App\DTOs\V1\WorkDTO;
use App\Http\Requests\V1\WorkRequest;
use App\Http\Resources\V1\WorkResource;
use App\Http\Resources\V1\WorkResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;

/**
 * @OA\Tag(
 *     name="Works",
 *     description="API Endpoints for Work operations"
 * )
 *
 * @OA\Schema(
 *     schema="Work",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="order", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="estimated_time", type="integer"),
 *     @OA\Property(property="dead_line", type="string", format="date-time"),
 *     @OA\Property(property="budget_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="WorkRequest",
 *     required={"order", "name", "estimated_time", "dead_line", "budget_id"},
 *     @OA\Property(property="order", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="estimated_time", type="integer"),
 *     @OA\Property(property="dead_line", type="string", format="date-time"),
 *     @OA\Property(property="budget_id", type="integer")
 * )
 *
 */
class WorkController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var WorkService Service for work data logic
     */
    protected WorkService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param WorkService $service
     */
    public function __construct(WorkService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/works",
     *     summary="Get all works",
     *     tags={"Works"},
     *     @OA\Response(
     *         response=200,
     *         description="List of works retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Work")
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
            new WorkResourceCollection($result),
            "Data retrieved successfully",
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/works/{id}",
     *     summary="Get work by ID",
     *     tags={"Works"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Work ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Work found",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Work not found"
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
            new WorkResource($result),
            "Data retrieved successfully",

        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/works",
     *     summary="Create a new work",
     *     tags={"Works"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Work created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     )
     * )
     */
    public function store(WorkRequest $request): JsonResponse
    {

        $workDTO = new WorkDTO(
            null,
            $request->input('order'),
            $request->input('name'),
            $request->input('notes'),
            $request->input('estimated_time'),
            dead_line: new Carbon($request->input('dead_line')),
            budget: new BudgetDTO(id:$request->input('budget_id')),
        );

        $result = $this->service->create($workDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new WorkResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/works/{id}",
     *     summary="Update an existing work",
     *     tags={"Works"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Work updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     )
     * )
     */
    public function update(int $id,WorkRequest $request): JsonResponse
    {
        $workDTO = new WorkDTO(
            $id,
            $request->input('order'),
            $request->input('name'),
            $request->input('notes'),
            $request->input('estimated_time'),
            dead_line: new Carbon($request->input('dead_line')),
            budget: new BudgetDTO(id:$request->input('budget_id')),
        );
        $result = $this->service->update($workDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new WorkResource($result),
            "Data updated successfully",
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/works/{id}",
     *     summary="Delete a work",
     *     tags={"Works"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Work deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Work not found"
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
     * @OA\Post(
     *     path="/api/v1/works/materials/{id}",
     *     summary="Add materials to a work",
     *     tags={"Works"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Work ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddMaterialsRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Materials added successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     )
     * )
     */
    public function addMaterials(AddMaterialsToWorksRequest $request) : JsonResponse
    {
        try {
            $work = $this->service->addMaterialsToWork($request);
            return $this->successResponse(new WorkResource($work), "Materials added successfully", Response::HTTP_CREATED);
        }catch (Exception $e){
            return $this->errorResponse("Controller Error: adding materials to work", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    /**
     * @OA\Post (
     *     path="/api/v1/works/states/{id}/{state}",
     *     summary="Change work state",
     *     tags={"Works"},
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
     *         @OA\JsonContent(ref="#/components/schemas/Work")
     *     )
     * )
     */
    public function changeState(int $id, string $state): JsonResponse
    {
        try {
            $work = $this->service->changeState($id, $state);
            return $this->successResponse(new WorkResource($work), "State changed successfully", Response::HTTP_OK);
        }catch (Exception $e) {
            return $this->errorResponse(
                "Controller Error: changing state of work",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getStates() : JsonResponse
    {
        try {
            return  $this->successResponse(Work::getStates(), "States retrieved successfully", Response::HTTP_OK);
        }catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
