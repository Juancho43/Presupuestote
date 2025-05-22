<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\BudgetDTO;
use App\Http\Requests\V1\AddMaterialsToWorksRequest;
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

/**
 * Work Controller
 *
 * Handles HTTP requests related to work records including CRUD operations
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
     * Get all work records
     *
     * @return JsonResponse Collection of work records
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
     * Get single work record by ID
     *
     * @param int $id Work record ID
     * @return JsonResponse Single work resource
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
     * Create new work record
     *
     * @param WorkRequest $request Validated Work data
     * @return JsonResponse Created work resource
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
     * Update existing work record
     *
     * @param WorkRequest $request Validated Work data
     * @return JsonResponse Updated work resource
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
     * Delete work record
     *
     * @param int $id Work record ID
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
    public function addMaterials(AddMaterialsToWorksRequest $request) : JsonResponse
    {
        try {
            $work = $this->service->addMaterialsToWork($request);
            return $this->successResponse(new WorkResource($work), "Materials added successfully", Response::HTTP_CREATED);
        }catch (Exception $e){
            return $this->errorResponse("Controller Error: adding materials to work", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
