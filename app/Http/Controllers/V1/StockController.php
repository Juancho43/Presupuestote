<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StockRequest;
use App\Http\Resources\V1\StockResource;
use App\Http\Resources\V1\StockResourceCollection;
use App\Repository\V1\StockRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stock Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class StockController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var StockRepository Repository for dummy data access
     */
    protected StockRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param StockRepository $StockRepository
     */
    public function __construct(StockRepository $StockRepository)
    {
        $this->repository = $StockRepository;
    }

    /**
     * Get all Stock records
     *
     * @return JsonResponse Collection of Stock records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new StockResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Stock record by ID
     *
     * @param int $id Stock record ID
     * @return JsonResponse Single Stock resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new StockResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Stock record
     *
     * @param StockRequest $request Validated Stock data
     * @return JsonResponse Created Stock resource
     * @throws Exception If creation fails
     */
    public function store(StockRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new StockResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Stock record
     *
     * @param StockRequest $request Validated Stock data
     * @return JsonResponse Updated Stock resource
     * @throws Exception If update fails
     */
    public function update(int $id,StockRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new StockResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Stock record
     *
     * @param int $id Stock record ID
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
