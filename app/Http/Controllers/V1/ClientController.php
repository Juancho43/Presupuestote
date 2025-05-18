<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\ClientRequest;
use App\Http\Resources\V1\ClientResource;
use App\Http\Resources\V1\ClientResourceCollection;
use App\Repository\V1\ClientRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client Controller
 *
 * Handles HTTP requests related to Client records including CRUD operations
 * and tag-based filtering.
 */
class ClientController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var ClientRepository Repository for dummy data access
     */
    protected ClientRepository $repository;

    /**
     * Initialize controller with repository dependency
     *
     * @param ClientRepository $ClientRepository
     */
    public function __construct(ClientRepository $ClientRepository)
    {
        $this->repository = $ClientRepository;
    }

    /**
     * Get all Client records
     *
     * @return JsonResponse Collection of Client records
     * @throws Exception If error occurs retrieving data
     */
    public function index() : JsonResponse
    {

        try{
            return $this->successResponse(new ClientResourceCollection($this->repository->all()), null, Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single Client record by ID
     *
     * @param int $id Client record ID
     * @return JsonResponse Single Client resource
     * @throws Exception If record not found or error occurs
     */
    public function show(int $id) : JsonResponse
    {
        try{
            return $this->successResponse(new ClientResource($this->repository->find($id)),null,Response::HTTP_OK);
        }catch(Exception $e){
            return $this->errorResponse("Error retrieving data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new Client record
     *
     * @param ClientRequest $request Validated Client data
     * @return JsonResponse Created Client resource
     * @throws Exception If creation fails
     */
    public function store(ClientRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->create($request);
            return $this->successResponse(new ClientResource($dummy),"Data stored successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error storing data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing Client record
     *
     * @param ClientRequest $request Validated Client data
     * @return JsonResponse Updated Client resource
     * @throws Exception If update fails
     */
    public function update(int $id,ClientRequest $request) : JsonResponse
    {
        try{
            $dummy = $this->repository->update($id,$request);
            return $this->successResponse(new ClientResource($dummy),"Data updated successfully" , Response::HTTP_CREATED);
        }catch(Exception $e){
            return $this->errorResponse("Error updating data",$e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Client record
     *
     * @param int $id Client record ID
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
