<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\CategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\CategoryResourceCollection;
use App\Repository\V1\CategoryRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Category Controller
 *
 * Handles HTTP requests related to dummy records including CRUD operations
 * and tag-based filtering.
 */
class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var CategoryRepository Repository for dummy data access
     */
    protected CategoryRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param CategoryRepository $CategoryRepository
     */
    public function __construct(CategoryRepository $CategoryRepository)
    {
        $this->repository = $CategoryRepository;
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
            return $this->successResponse(new CategoryResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single dummy record by ID
     *
     * @param int $id Category record ID
     * @return JsonResponse Single dummy resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new CategoryResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new dummy record
     *
     * @param CategoryRequest $request Validated dummy data
     * @return JsonResponse Created dummy resource
     * @throws Exception If creation fails
     */
    public function store(CategoryRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new CategoryResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing dummy record
     *
     * @param CategoryRequest $request Validated dummy data
     * @return JsonResponse Updated dummy resource
     * @throws Exception If update fails
     */
    public function update(CategoryRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($request->id,$request);
            return $this->successResponse(new CategoryResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete dummy record
     *
     * @param int $id Category record ID
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
