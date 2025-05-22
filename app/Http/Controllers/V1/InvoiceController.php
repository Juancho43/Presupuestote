<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\SupplierDTO;
use App\Http\Requests\V1\AddMaterialsToInvoiceRequest;
use App\Services\V1\InvoiceService;
use App\DTOs\V1\InvoiceDTO;
use App\Http\Requests\V1\InvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Resources\V1\InvoiceResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice Controller
 *
 * Handles HTTP requests related to invoice records including CRUD operations
 */
class InvoiceController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var InvoiceService Service for invoice data logic
     */
    protected InvoiceService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param InvoiceService $service
     */
    public function __construct(InvoiceService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all invoice records
     *
     * @return JsonResponse Collection of invoice records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new InvoiceResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single invoice record by ID
     *
     * @param int $id Invoice record ID
     * @return JsonResponse Single invoice resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new InvoiceResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new invoice record
     *
     * @param InvoiceRequest $request Validated Invoice data
     * @return JsonResponse Created invoice resource
     */
    public function store(InvoiceRequest $request): JsonResponse
    {


        $invoiceDTO = new InvoiceDTO(date: $request->date, supplier: new SupplierDTO(id:$request->supplier_id));

        $result = $this->service->create($invoiceDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new InvoiceResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing invoice record
     *
     * @param InvoiceRequest $request Validated Invoice data
     * @return JsonResponse Updated invoice resource
     */
    public function update(int $id,InvoiceRequest $request): JsonResponse
    {
        $invoiceDTO = new InvoiceDTO(id: $id,date: $request->date, supplier: new SupplierDTO(id:$request->supplier_id));
        $result = $this->service->update($invoiceDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new InvoiceResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete invoice record
     *
     * @param int $id Invoice record ID
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

    public function updateInvoiceTotal(int $id) : JsonResponse
    {

        $result = $this->service->get($id)->updateTotal();

        if ($result instanceof JsonResponse) {
            return $result;
        }
        return $this->successResponse(
            new InvoiceResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    public function addMaterials(AddMaterialsToInvoiceRequest $request) : JsonResponse
    {

        $result = $this->service->addMaterialsToInvoice($request);

        if ($result instanceof JsonResponse) {
            return $result;
        }
        return $this->successResponse(
            new InvoiceResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }
}
