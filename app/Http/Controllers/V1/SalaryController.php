<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\SalaryRequest;
use App\Http\Resources\V1\SalaryResource;
use App\Http\Resources\V1\SalaryResourceCollection;
use App\Repository\V1\SalaryRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Salary Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class SalaryController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var SalaryRepository Repository for Salary data access
     */
    protected SalaryRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param SalaryRepository $SalaryRepository
     */
    public function __construct(SalaryRepository $SalaryRepository)
    {
        $this->repository = $SalaryRepository;
    }

    /**
     * Get all Salary records
     *
     * @return JsonResponse Collection of Salary records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new SalaryResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Salary record by ID
     *
     * @param int $id Salary record ID
     * @return JsonResponse Single Salary resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new SalaryResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Salary record
     *
     * @param SalaryRequest $request Validated Salary data
     * @return JsonResponse Created Salary resource
     * @throws Exception If creation fails
     */
    public function store(SalaryRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new SalaryResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Salary record
     *
     * @param SalaryRequest $request Validated Salary data
     * @return JsonResponse Updated Salary resource
     * @throws Exception If update fails
     */
    public function update(int $id, SalaryRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new SalaryResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Salary record
     *
     * @param int $id Salary record ID
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
