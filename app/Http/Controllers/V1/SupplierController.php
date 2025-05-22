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
 * Supplier Controller
 *
 * Handles HTTP requests related to supplier records including CRUD operations
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
     * Get all supplier records
     *
     * @return JsonResponse Collection of supplier records
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
     * Get single supplier record by ID
     *
     * @param int $id Supplier record ID
     * @return JsonResponse Single supplier resource
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
     * Create new supplier record
     *
     * @param SupplierRequest $request Validated Supplier data
     * @return JsonResponse Created supplier resource
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
     * Update existing supplier record
     *
     * @param SupplierRequest $request Validated Supplier data
     * @return JsonResponse Updated supplier resource
     */
    public function update(int $id,SupplierRequest $request): JsonResponse
    {
        $supplierDTO = new SupplierDTO(
            $id,
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
     * Delete supplier record
     *
     * @param int $id Supplier record ID
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
