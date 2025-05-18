<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\PersonRequest;
use App\Http\Resources\V1\PersonResource;
use App\Http\Resources\V1\PersonResourceCollection;
use App\Repository\V1\PersonRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Person Controller
 *
 * Handles HTTP requests related to Person records including CRUD operations
 * and tag-based filtering.
 */
class PersonController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PersonRepository Repository for Person data access
     */
    protected PersonRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param PersonRepository $PersonRepository
     */
    public function __construct(PersonRepository $PersonRepository)
    {
        $this->repository = $PersonRepository;
    }

    /**
     * Get all Person records
     *
     * @return JsonResponse Collection of Person records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new PersonResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Person record by ID
     *
     * @param int $id Person record ID
     * @return JsonResponse Single Person resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new PersonResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Person record
     *
     * @param PersonRequest $request Validated Person data
     * @return JsonResponse Created Person resource
     * @throws Exception If creation fails
     */
    public function store(PersonRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new PersonResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Person record
     *
     * @param PersonRequest $request Validated Person data
     * @return JsonResponse Updated Person resource
     * @throws Exception If update fails
     */
    public function update(int $id, PersonRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new PersonResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Person record
     *
     * @param int $id Person record ID
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
