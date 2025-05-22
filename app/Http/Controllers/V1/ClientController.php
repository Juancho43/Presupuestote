<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Services\V1\ClientService;
use App\DTOs\V1\ClientDTO;
use App\Http\Requests\V1\ClientRequest;
use App\Http\Resources\V1\ClientResource;
use App\Http\Resources\V1\ClientResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpFoundation\Response;

/**
 * Client Controller
 *
 * Handles HTTP requests related to client records including CRUD operations
 */
class ClientController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var ClientService Service for client data logic
     */
    protected ClientService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param ClientService $service
     */
    public function __construct(ClientService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all client records
     *
     * @return JsonResponse Collection of client records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new ClientResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single client record by ID
     *
     * @param int $id Client record ID
     * @return JsonResponse Single client resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new ClientResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new client record
     *
     * @param ClientRequest $request Validated client data
     * @return JsonResponse Created client resource
     */
    public function store(ClientRequest $request): JsonResponse
    {

        $clientDTO = new ClientDTO(
            null,
            new Decimal($request->input('balance')),
            new PersonDTO(
                $request->input('person_id'),
                $request->input('person.name'),
                $request->input('person.last_name'),
                $request->input('person.address'),
                $request->input('person.phone_number'),
                $request->input('person.mail'),
                $request->input('person.dni'),
                $request->input('person.cuit')
            )
        );

        $result = $this->service->create($clientDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new ClientResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing client record
     *
     * @param ClientRequest $request Validated client data
     * @return JsonResponse Updated client resource
     */
    public function update(int $id, ClientRequest $request): JsonResponse
    {
         $clientDTO = new ClientDTO(
            $id,
            new Decimal($request->input('balance')),
            new PersonDTO(
                $request->input('person_id'),
                $request->input('person.name'),
                $request->input('person.last_name'),
                $request->input('person.address'),
                $request->input('person.phone_number'),
                $request->input('person.mail'),
                $request->input('person.dni'),
                $request->input('person.cuit')
            )
        );
        $result = $this->service->update($clientDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new ClientResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete client record
     *
     * @param int $id Client record ID
     * @return JsonResponse Empty response on success
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->service->delete($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            "Data deleted successfully",
            Response::HTTP_NO_CONTENT
        );
    }
}
