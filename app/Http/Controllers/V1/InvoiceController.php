<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\InvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Resources\V1\InvoiceResourceCollection;
use App\Repository\V1\InvoiceRepository;
use App\Services\V1\InvoiceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice Controller
 *
 * Handles HTTP requests related to Invoice records including CRUD operations
 * and tag-based filtering.
 */
class InvoiceController extends Controller
{
    use ApiResponseTrait;
    protected InvoiceService $service;

    /**
     * @var InvoiceRepository Repository for Invoice data access
     */
    protected InvoiceRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param InvoiceRepository $InvoiceRepository
     */
    public function __construct(InvoiceRepository $InvoiceRepository, InvoiceService $service)
    {
        $this->service = $service;
        $this->repository = $InvoiceRepository;
    }

    /**
     * Get all Invoice records
     *
     * @return JsonResponse Collection of Invoice records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new InvoiceResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Invoice record by ID
     *
     * @param int $id Invoice record ID
     * @return JsonResponse Single Invoice resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new InvoiceResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Invoice record
     *
     * @param InvoiceRequest $request Validated Invoice data
     * @return JsonResponse Created Invoice resource
     * @throws Exception If creation fails
     */
    public function store(InvoiceRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new InvoiceResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Invoice record
     *
     * @param InvoiceRequest $request Validated Invoice data
     * @return JsonResponse Updated Invoice resource
     * @throws Exception If update fails
     */
    public function update(int $id,InvoiceRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new InvoiceResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Invoice record
     *
     * @param int $id Invoice record ID
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
    public function updateInvoiceTotal(int $id) : JsonResponse
    {
        try{
            $dummy = $this->service->updateInvoiceTotal($id);
            return $this->successResponse(new InvoiceResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



}
