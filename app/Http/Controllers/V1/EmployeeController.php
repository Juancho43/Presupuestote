<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Http\Requests\V1\PersonUpdateRequest;
use App\Services\V1\EmployeeService;
use App\DTOs\V1\EmployeeDTO;
use App\Http\Requests\V1\EmployeeRequest;
use App\Http\Resources\V1\EmployeeResource;
use App\Http\Resources\V1\EmployeeResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Tag(
 *     name="Employees",
 *     description="API Endpoints for Employee operations"
 * )
 *
 * @OA\Schema(
 *     schema="Employee",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="salary", type="number", format="float"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person")
 * )
 *
 * @OA\Schema(
 *     schema="EmployeeRequest",
 *     required={"salary", "start_date", "is_active", "person"},
 *     @OA\Property(property="salary", type="number", format="float"),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(
 *         property="person",
 *         type="object",
 *         required={"name", "last_name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="last_name", type="string"),
 *         @OA\Property(property="address", type="string"),
 *         @OA\Property(property="phone_number", type="string"),
 *         @OA\Property(property="mail", type="string"),
 *         @OA\Property(property="dni", type="string"),
 *         @OA\Property(property="cuit", type="string")
 *     )
 * )
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
     * @OA\Get(
     *     path="/api/v1/employees",
     *     summary="Get all employees",
     *     tags={"Employees"},
     *     @OA\Response(
     *         response=200,
     *         description="List of employees retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Employee")
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
            new EmployeeResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/employees/{id}",
     *     summary="Get employee by ID",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee found",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
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
            new EmployeeResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/employees",
     *     summary="Create a new employee",
     *     tags={"Employees"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EmployeeRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Employee created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     )
     * )
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $employeeDTO = new EmployeeDTO(
            null,
            $request->input('salary'),
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
     * @OA\Put(
     *     path="/api/v1/employees/{id}",
     *     summary="Update an existing employee",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="last_name", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="phone_number", type="string"),
     *              @OA\Property(property="mail", type="string"),
     *              @OA\Property(property="dni", type="string"),
     *              @OA\Property(property="cuit", type="string")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     )
     * )
     */
    public function update(int $id,PersonUpdateRequest $request): JsonResponse
    {
        $employeeDTO = new EmployeeDTO(
            $id,
            $request->input('salary'),
            new Carbon($request->input('start_date')),
            new Carbon($request->input('end_date')),
            $request->input('is_active'),
            new PersonDTO(
                $id,
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
     * @OA\Delete(
     *     path="/api/v1/employees/{id}",
     *     summary="Delete an employee",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Employee deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
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
}
