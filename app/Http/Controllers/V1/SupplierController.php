<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\SupplierRequest;
use App\Http\Resources\V1\SupplierResource;
use App\Http\Resources\V1\SupplierResourceCollection;
use App\Repository\V1\SupplierRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Supplier Controller
 *
 * Handles HTTP requests related to Supplier records including CRUD operations
 * and tag-based filtering.
 */
class SupplierController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var SupplierRepository Repository for Supplier data access
     */
    protected SupplierRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param SupplierRepository $SupplierRepository
     */
    public function __construct(SupplierRepository $SupplierRepository)
    {
        $this->repository = $SupplierRepository;
    }

    /**
     * Get all Supplier records
     *
     * @return JsonResponse Collection of Supplier records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new SupplierResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Supplier record by ID
     *
     * @param int $id Supplier record ID
     * @return JsonResponse Single Supplier resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new SupplierResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Supplier record
     *
     * @param SupplierRequest $request Validated Supplier data
     * @return JsonResponse Created Supplier resource
     * @throws Exception If creation fails
     */
    public function store(SupplierRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new SupplierResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Supplier record
     *
     * @param SupplierRequest $request Validated Supplier data
     * @return JsonResponse Updated Supplier resource
     * @throws Exception If update fails
     */
    public function update(int $id,SupplierRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new SupplierResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Supplier record
     *
     * @param int $id Supplier record ID
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
