<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\AddMaterialsToWorksRequest;
use App\Http\Requests\V1\WorkRequest;
use App\Http\Resources\V1\WorkResource;
use App\Http\Resources\V1\WorkResourceCollection;
use App\Repository\V1\WorkRepository;
use App\Services\V1\WorkService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\directoryExists;

/**
 * Work Controller
 *
 * Handles HTTP requests related to Work records including CRUD operations
 * and tag-based filtering.
 */
class WorkController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var WorkRepository Repository for Work data access
     */
    protected WorkRepository $repository;

    protected WorkService $service;
    /**
     * Initialize controller with Repository dependency
     *
     * @param WorkRepository $WorkRepository
     */


    public function __construct(WorkRepository $WorkRepository, WorkService $WorkService)
    {
        $this->service = $WorkService;
        $this->repository = $WorkRepository;
    }

    /**
     * Get all Work records
     *
     * @return JsonResponse Collection of Work records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new WorkResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Work record by ID
     *
     * @param int $id Work record ID
     * @return JsonResponse Single Work resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new WorkResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Work record
     *
     * @param WorkRequest $request Validated Work data
     * @return JsonResponse Created Work resource
     * @throws Exception If creation fails
     */
    public function store(WorkRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new WorkResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Update existing Work record
     *
     * @param WorkRequest $request Validated Work data
     * @return JsonResponse Updated Work resource
     * @throws Exception If update fails
     */
    public function update(WorkRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new WorkResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Work record
     *
     * @param int $id Work record ID
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

    public function addMaterials(AddMaterialsToWorksRequest $request) : JsonResponse
    {
        try {
            $work = $this->service->addMaterialsToWork($request);
            return $this->successResponse(new WorkResource($work), "Materials added successfully", Response::HTTP_CREATED);
        }catch (Exception $e){
            return $this->errorResponse("Error adding materials to work", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}
