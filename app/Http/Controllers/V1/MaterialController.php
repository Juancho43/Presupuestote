<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\MaterialRequest;
use App\Http\Resources\V1\MaterialResource;
use App\Http\Resources\V1\MaterialResourceCollection;
use App\Repository\V1\MaterialRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Material Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class MaterialController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var MaterialRepository Repository for Material data access
     */
    protected MaterialRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param MaterialRepository $MaterialRepository
     */
    public function __construct(MaterialRepository $MaterialRepository)
    {
        $this->repository = $MaterialRepository;
    }

    /**
     * Get all Material records
     *
     * @return JsonResponse Collection of Material records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new MaterialResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Material record by ID
     *
     * @param int $id Material record ID
     * @return JsonResponse Single Material resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new MaterialResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Material record
     *
     * @param MaterialRequest $request Validated Material data
     * @return JsonResponse Created Material resource
     * @throws Exception If creation fails
     */
    public function store(MaterialRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new MaterialResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Material record
     *
     * @param MaterialRequest $request Validated Material data
     * @return JsonResponse Updated Material resource
     * @throws Exception If update fails
     */
    public function update(MaterialRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new MaterialResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Material record
     *
     * @param int $id Material record ID
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
