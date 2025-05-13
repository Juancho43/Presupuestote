<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\PaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Http\Resources\V1\PaymentResourceCollection;
use App\Repository\V1\PaymentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class PaymentController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PaymentRepository Repository for dummy data access
     */
    protected PaymentRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param PaymentRepository $PaymentRepository
     */
    public function __construct(PaymentRepository $PaymentRepository)
    {
        $this->repository = $PaymentRepository;
    }

    /**
     * Get all dummy records
     *
     * @return JsonResponse Collection of dummy records
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
     * Get single dummy record by ID
     *
     * @param int $id Payment record ID
     * @return JsonResponse Single dummy resource
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
     * Create new dummy record
     *
     * @param PaymentRequest $request Validated dummy data
     * @return JsonResponse Created dummy resource
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
     * Update existing dummy record
     *
     * @param PaymentRequest $request Validated dummy data
     * @return JsonResponse Updated dummy resource
     * @throws Exception If update fails
     */
    public function update(PaymentRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new PaymentResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete dummy record
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

}
