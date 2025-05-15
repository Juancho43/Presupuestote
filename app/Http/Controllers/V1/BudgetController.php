<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\BudgetRequest;
use App\Http\Resources\V1\BudgetResource;
use App\Http\Resources\V1\BudgetResourceCollection;
use App\Repository\V1\BudgetRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Budget Controller
 *
 * Handles HTTP requests related to Budget records including CRUD operations
 * and tag-based filtering.
 */
class BudgetController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var BudgetRepository Repository for Budget data access
     */
    protected BudgetRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param BudgetRepository $BudgetRepository
     */
    public function __construct(BudgetRepository $BudgetRepository)
    {
        $this->repository = $BudgetRepository;
    }

    /**
     * Get all Budget records
     *
     * @return JsonResponse Collection of Budget records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new BudgetResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Budget record by ID
     *
     * @param int $id Budget record ID
     * @return JsonResponse Single Budget resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new BudgetResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Budget record
     *
     * @param BudgetRequest $request Validated Budget data
     * @return JsonResponse Created Budget resource
     * @throws Exception If creation fails
     */
    public function store(BudgetRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new BudgetResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Budget record
     *
     * @param BudgetRequest $request Validated Budget data
     * @return JsonResponse Updated Budget resource
     * @throws Exception If update fails
     */
    public function update(BudgetRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new BudgetResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Budget record
     *
     * @param int $id Budget record ID
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
