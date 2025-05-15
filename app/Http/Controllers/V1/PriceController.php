<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\PriceRequest;
use App\Http\Resources\V1\PriceResource;
use App\Http\Resources\V1\PriceResourceCollection;
use App\Repository\V1\PriceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Price Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class PriceController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PriceRepository Repository for dummy data access
     */
    protected PriceRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param PriceRepository $PriceRepository
     */
    public function __construct(PriceRepository $PriceRepository)
    {
        $this->repository = $PriceRepository;
    }

    /**
     * Get all Price records
     *
     * @return JsonResponse Collection of Price records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new PriceResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Price record by ID
     *
     * @param int $id Price record ID
     * @return JsonResponse Single Price resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PriceResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Price record
     *
     * @param PriceRequest $request Validated Price data
     * @return JsonResponse Created Price resource
     * @throws Exception If creation fails
     */
    public function store(PriceRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new PriceResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Price record
     *
     * @param PriceRequest $request Validated Price data
     * @return JsonResponse Updated Price resource
     * @throws Exception If update fails
     */
    public function update(PriceRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new PriceResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Price record
     *
     * @param int $id Price record ID
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
