<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Services\V1\EmployeeService;
use App\DTOs\V1\EmployeeDTO;
use App\Http\Requests\V1\EmployeeRequest;
use App\Http\Resources\V1\EmployeeResource;
use App\Http\Resources\V1\EmployeeResourceCollection;
use Carbon\Carbon;
use Ramsey\Uuid\Type\Decimal;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Employee Controller
 *
 * Handles HTTP requests related to employee records including CRUD operations
 */
class EmployeeController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var EmployeeService Service for employee data logic
     */
    protected EmployeeService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param EmployeeService $service
     */
    public function __construct(EmployeeService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all employee records
     *
     * @return JsonResponse Collection of employee records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new EmployeeResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single employee record by ID
     *
     * @param int $id Employee record ID
     * @return JsonResponse Single employee resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new EmployeeResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new employee record
     *
     * @param EmployeeRequest $request Validated Employee data
     * @return JsonResponse Created employee resource
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $employeeDTO = new EmployeeDTO(
            null,
            new Decimal($request->input('salary')),
            new Carbon($request->input('start_date')),
            new Carbon($request->input('end_date')),
            $request->input('is_active'),
            new PersonDTO(
                $request->input('person_id'),
                $request->input('person.name'),
                $request->input('person.last_name'),
                $request->input('person.address'),
                $request->input('person.phone_number'),
                $request->input('person.mail'),
                $request->input('person.dni'),
                $request->input('person.cuit')
            )
        );


        $result = $this->service->create($employeeDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new EmployeeResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing employee record
     *
     * @param EmployeeRequest $request Validated Employee data
     * @return JsonResponse Updated employee resource
     */
    public function update(int $id,EmployeeRequest $request): JsonResponse
    {
        $employeeDTO = new EmployeeDTO(
            $id,
            new Decimal($request->input('salary')),
            new Carbon($request->input('start_date')),
            new Carbon($request->input('end_date')),
            $request->input('is_active'),
            new PersonDTO(
                $request->input('person_id'),
                $request->input('person.name'),
                $request->input('person.last_name'),
                $request->input('person.address'),
                $request->input('person.phone_number'),
                $request->input('person.mail'),
                $request->input('person.dni'),
                $request->input('person.cuit')
            )
        );
        $result = $this->service->update($employeeDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new EmployeeResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete employee record
     *
     * @param int $id Employee record ID
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
