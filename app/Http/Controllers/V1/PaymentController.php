<?php
namespace App\Http\Controllers\V1;

use App\Services\V1\PaymentService;
use App\DTOs\V1\PaymentDTO;
use App\Http\Requests\V1\PaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Http\Resources\V1\PaymentResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="API Endpoints for Payment operations"
 * )
 *
 * @OA\Schema(
 *     schema="Payment",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="payable_type", type="string"),
 *     @OA\Property(property="payable_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="PaymentRequest",
 *     required={"amount", "date", "payable_type", "payable_id"},
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="payable_type", type="string"),
 *     @OA\Property(property="payable_id", type="integer")
 * )
 */
class PaymentController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PaymentService Service for payment data logic
     */
    protected PaymentService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param PaymentService $service
     */
    public function __construct(PaymentService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     summary="Get all payments",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Payment")
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
            new PaymentResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/{id}",
     *     summary="Get payment by ID",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment found",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
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
            new PaymentResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PaymentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     )
     * )
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        $paymentDTO = new PaymentDTO(null,
            $request->input('amount'),
            new Carbon($request->input('date')),
            $request->input('description'),
            $request->input('payable_type'),
            $request->input('payable_id')
        );
        $result = $this->service->create($paymentDTO);

        if ($result->getStatusCode() == Response::HTTP_INTERNAL_SERVER_ERROR) {
            return $result;
        }

        return $result;
    }

    /**
     * @OA\Put(
     *     path="/api/v1/payments/{id}",
     *     summary="Update an existing payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PaymentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     )
     * )
     */
    public function update(int $id,PaymentRequest $request): JsonResponse
    {
        $paymentDTO = new PaymentDTO(
            $id,
            $request->input('amount'),
            new Carbon($request->input('date')),
            $request->input('description'),
            $request->input('payable_type'),
            $request->input('payable_id')
        );
        $result = $this->service->update($paymentDTO);

        if ($result->getStatusCode() == Response::HTTP_INTERNAL_SERVER_ERROR) {
            return $result;
        }

        return $result;
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/payments/{id}",
     *     summary="Delete a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Payment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
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
     *     path="/api/v1/payments/client/{id}",
     *     summary="Get all payments for a client",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of client payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Payment")
     *             )
     *         )
     *     )
     * )
     */
    public function allClientPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allClientPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/payments/employee/{id}",
     *     summary="Get all payments for an employee",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employee payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Payment")
     *             )
     *         )
     *     )
     * )
     */
    public function allEmployeePayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allEmployeePayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/supplier/{id}",
     *     summary="Get all payments for a supplier",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of supplier payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Payment")
     *             )
     *         )
     *     )
     * )
     */
    public function allSupplierPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allSupplierPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
