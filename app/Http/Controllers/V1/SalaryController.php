<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\EmployeeDTO;
use App\Services\V1\SalaryService;
use App\DTOs\V1\SalaryDTO;
use App\Http\Requests\V1\SalaryRequest;
use App\Http\Resources\V1\SalaryResource;
use App\Http\Resources\V1\SalaryResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Salary Controller
 *
 * Handles HTTP requests related to salary records including CRUD operations
 */
class SalaryController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var SalaryService Service for salary data logic
     */
    protected SalaryService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param SalaryService $service
     */
    public function __construct(SalaryService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all salary records
     *
     * @return JsonResponse Collection of salary records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SalaryResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single salary record by ID
     *
     * @param int $id Salary record ID
     * @return JsonResponse Single salary resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SalaryResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new salary record
     *
     * @param SalaryRequest $request Validated Salary data
     * @return JsonResponse Created salary resource
     */
    public function store(SalaryRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $salaryDTO = new SalaryDTO(null,
        $request->input('amount'),
        new Carbon($request->input('date')),
        $request->input('active'),
        null,
        new EmployeeDTO(id: $request->input('employee_id')),
    );

        // Call service to create the salary record

    $result = $this->service->create($salaryDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SalaryResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing salary record
     *
     * @param SalaryRequest $request Validated Salary data
     * @return JsonResponse Updated salary resource
     */
    public function update(int $id,SalaryRequest $request): JsonResponse
    {
        $salaryDTO = new SalaryDTO(
            $id,
            $request->input('amount'),
            new Carbon($request->input('date')),
            $request->input('active'),
            null,
            new EmployeeDTO(id: $request->input('employee_id')),
        );
        $result = $this->service->update($salaryDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SalaryResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete salary record
     *
     * @param int $id Salary record ID
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
}
