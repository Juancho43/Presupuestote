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
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment Controller
 *
 * Handles HTTP requests related to payment records including CRUD operations
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
     * Get all payment records
     *
     * @return JsonResponse Collection of payment records
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
     * Get single payment record by ID
     *
     * @param int $id Payment record ID
     * @return JsonResponse Single payment resource
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
     * Create new payment record
     *
     * @param PaymentRequest $request Validated Payment data
     * @return JsonResponse Created payment resource
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        $paymentDTO = new PaymentDTO(null,
            new Decimal($request->input('amount')),
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
     * Update existing payment record
     *
     * @param PaymentRequest $request Validated Payment data
     * @return JsonResponse Updated payment resource
     */
    public function update(int $id,PaymentRequest $request): JsonResponse
    {
        $paymentDTO = new PaymentDTO(
            $id,
            new Decimal($request->input('amount')),
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
     * Delete payment record
     *
     * @param int $id Payment record ID
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


    public function allClientPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allClientPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allEmployeePayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allEmployeePayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allSupplierPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->service->allSupplierPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
