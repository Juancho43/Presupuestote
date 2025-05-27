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
 * @OA\Tag(
 *     name="Salaries",
 *     description="API Endpoints for Salary operations"
 * )
 *
 * @OA\Schema(
 *     schema="Salary",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="employee_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="SalaryRequest",
 *     required={"amount", "date", "employee_id", "active"},
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="employee_id", type="integer")
 * )
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
     * @OA\Get(
     *     path="/api/v1/salaries",
     *     summary="Get all salaries",
     *     tags={"Salaries"},
     *     @OA\Response(
     *         response=200,
     *         description="List of salaries retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Salary")
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
            new SalaryResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/salaries/{id}",
     *     summary="Get salary by ID",
     *     tags={"Salaries"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Salary ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salary found",
     *         @OA\JsonContent(ref="#/components/schemas/Salary")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Salary not found"
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
            new SalaryResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }


    /**
     * @OA\Post(
     *     path="/api/v1/salaries",
     *     summary="Create a new salary",
     *     tags={"Salaries"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SalaryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Salary created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Salary")
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/v1/salaries/{id}",
     *     summary="Update an existing salary",
     *     tags={"Salaries"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SalaryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salary updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Salary")
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/v1/salaries/{id}",
     *     summary="Delete a salary",
     *     tags={"Salaries"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Salary deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Salary not found"
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
