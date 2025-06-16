<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\SupplierDTO;
use App\Http\Requests\V1\AddMaterialsToInvoiceRequest;
use App\Services\V1\InvoiceService;
use App\DTOs\V1\InvoiceDTO;
use App\Http\Requests\V1\InvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Resources\V1\InvoiceResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
//@OA\Property(property="materials", type="array", @OA\Items(ref="#/components/schemas/Material"))
/**
 * @OA\Tag(
 *     name="Invoices",
 *     description="API Endpoints for Invoice operations"
 * )
 *
 * @OA\Schema(
 *     schema="Invoice",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="total", type="number", format="float"),
 *     @OA\Property(property="supplier_id", type="integer"),
 *
 * )
 *
 * @OA\Schema(
 *     schema="InvoiceRequest",
 *     required={"date", "supplier_id"},
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="supplier_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="AddMaterialsRequest",
 *     required={"invoice_id", "materials"},
 *     @OA\Property(property="invoice_id", type="integer"),
 *     @OA\Property(
 *         property="materials",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="quantity", type="number"),
 *             @OA\Property(property="price", type="number")
 *         )
 *     )
 * )
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
     * @OA\Get(
     *     path="/api/v1/invoices",
     *     summary="Get all invoices",
     *     tags={"Invoices"},
     *     @OA\Response(
     *         response=200,
     *         description="List of invoices retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Invoice")
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index(int $page): JsonResponse
    {
        $result = $this->service->getAll($page);

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
     * @OA\Get(
     *     path="/api/v1/invoices/{id}",
     *     summary="Get invoice by ID",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Invoice ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice found",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found"
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
            new InvoiceResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/invoices",
     *     summary="Create a new invoice",
     *     tags={"Invoices"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     )
     * )
     */
    public function store(InvoiceRequest $request): JsonResponse
    {


        $invoiceDTO = new InvoiceDTO(
            date: new Carbon($request->date),
            supplier: new SupplierDTO(id:$request->supplier_id),
            description: $request->description ?? null,
        );

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
     * @OA\Put(
     *     path="/api/v1/invoices/{id}",
     *     summary="Update an existing invoice",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InvoiceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     )
     * )
     */
    public function update(int $id,InvoiceRequest $request): JsonResponse
    {
        $invoiceDTO = new InvoiceDTO(
            id: $id,
            date: new Carbon($request->date),
            supplier: new SupplierDTO(id:$request->supplier_id),
            description: $request->description ?? null,);
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
     * @OA\Delete(
     *     path="/api/v1/invoices/{id}",
     *     summary="Delete an invoice",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Invoice deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice not found"
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
     * @OA\Get(
     *     path="/api/v1/invoices/updateTotal/{id}",
     *     summary="Update invoice total",
     *     tags={"Invoices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice total updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/v1/invoices/materials",
     *     summary="Add materials to invoice",
     *     tags={"Invoices"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddMaterialsRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Materials added to invoice successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Invoice")
     *     )
     * )
     */
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
