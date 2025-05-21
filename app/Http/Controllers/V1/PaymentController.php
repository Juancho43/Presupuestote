<?php

namespace App\Http\Controllers\V1;


use App\Http\Requests\V1\PaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Http\Resources\V1\PaymentResourceCollection;
use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Salary;
use App\Repository\V1\PaymentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Services\V1\PaymentService;

/**
 * Payment Controller
 *
 * Handles HTTP requests related to Payment records including CRUD operations
 * and tag-based filtering.
 */
class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected PaymentService $service;

    /**
     * @var PaymentRepository Repository for Payment data access
     */
    protected PaymentRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param PaymentRepository $PaymentRepository
     */
    public function __construct(PaymentRepository $PaymentRepository, PaymentService $PaymentService)
    {
        $this->service = $PaymentService;
        $this->repository = $PaymentRepository;
    }

    /**
     * Get all Payment records
     *
     * @return JsonResponse Collection of Payment records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new PaymentResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Payment record by ID
     *
     * @param int $id Payment record ID
     * @return JsonResponse Single Payment resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Payment record
     *
     * @param PaymentRequest $request Validated Payment data
     * @return JsonResponse Created Payment resource
     * @throws Exception If creation fails
     */
    public function store(PaymentRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new PaymentResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Payment record
     *
     * @param PaymentRequest $request Validated Payment data
     * @return JsonResponse Updated Payment resource
     * @throws Exception If update fails
     */
    public function update(int $id, PaymentRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new PaymentResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Payment record
     *
     * @param int $id Payment record ID
     * @return JsonResponse Empty response on success
     * @throws Exception If deletion fails
     */
    public function destroy(int $id) : JsonResponse
    {
        try{
            $this->repository->delete($id);
            return $this->successResponse(null, null, Response::HTTP_NO_CONTENT);
        }catch(Exception $e){
            return $this->errorResponse("Error deleting data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allClientPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->repository->allClientPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allEmployeePayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->repository->allEmployeePayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allSupplierPayments(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResourceCollection($this->repository->allSupplierPayments($id)), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addPaymentToBudget(PaymentRequest $request) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResource($this->service->processModelPayment(Budget::class, $request, 'budget')), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Controller Error: can't store data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addPaymentToInvoice(PaymentRequest $request) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResource($this->service->processModelPayment(Invoice::class, $request, 'invoice')), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Controller Error: can't store data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addPaymentToSalary(PaymentRequest $request) : JsonResponse
    {
        try{
            return $this->successResponse(new PaymentResource($this->service->processModelPayment(Salary::class, $request, 'salary')), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Controller Error: can't store data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
