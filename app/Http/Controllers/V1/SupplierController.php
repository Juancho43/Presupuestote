<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Services\V1\SupplierService;
use App\DTOs\V1\SupplierDTO;
use App\Http\Requests\V1\SupplierRequest;
use App\Http\Resources\V1\SupplierResource;
use App\Http\Resources\V1\SupplierResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Tag(
 *     name="Suppliers",
 *     description="API Endpoints for Supplier operations"
 * )
 *
 * @OA\Schema(
 *     schema="Supplier",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="person", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="last_name", type="string"),
 *         @OA\Property(property="address", type="string"),
 *         @OA\Property(property="phone_number", type="string"),
 *         @OA\Property(property="mail", type="string"),
 *         @OA\Property(property="dni", type="string"),
 *         @OA\Property(property="cuit", type="string")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="SupplierRequest",
 *     required={"notes", "person_id", "person"},
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="person_id", type="integer"),
 *     @OA\Property(property="person", type="object",
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
class SupplierController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var SupplierService Service for supplier data logic
     */
    protected SupplierService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param SupplierService $service
     */
    public function __construct(SupplierService $service)
    {
        $this->service = $service->getInstance();
    }


    /**
     * @OA\Get(
     *     path="/api/v1/suppliers",
     *     summary="Get all suppliers",
     *     tags={"Suppliers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of suppliers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Supplier")
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
            new SupplierResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/suppliers/{id}",
     *     summary="Get supplier by ID",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier found",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found"
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
            new SupplierResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/suppliers",
     *     summary="Create a new supplier",
     *     tags={"Suppliers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Supplier created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     )
     * )
     */
    public function store(SupplierRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $supplierDTO = new SupplierDTO(
        null,
        $request->input('notes'),
        null,
        new PersonDTO(
            $request->input('person_id'),
            $request->input('person.name'),
            $request->input('person.last_name'),
            $request->input('person.address'),
            $request->input('person.phone_number'),
            $request->input('person.mail'),
            $request->input('person.dni'),
            $request->input('person.cuit')
    ));


    $result = $this->service->create($supplierDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SupplierResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/suppliers/{id}",
     *     summary="Update an existing supplier",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *  @OA\RequestBody(
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
     *         description="Supplier updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Supplier")
     *     )
     * )
     */
    public function update(int $id,SupplierRequest $request): JsonResponse
    {
        $supplierDTO = new SupplierDTO(
            $id,
            $request->input('notes'),
            null,
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
            ));
        $result = $this->service->update($supplierDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SupplierResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/suppliers/{id}",
     *     summary="Delete a supplier",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Supplier deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found"
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
