<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Http\Requests\V1\PersonUpdateRequest;
use App\Services\V1\ClientService;
use App\DTOs\V1\ClientDTO;
use App\Http\Requests\V1\ClientRequest;
use App\Http\Resources\V1\ClientResource;
use App\Http\Resources\V1\ClientResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Tag(
 *     name="Clients",
 *     description="API Endpoints for Client operations"
 * )
 *
 * @OA\Schema(
 *     schema="Person",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="mail", type="string"),
 *     @OA\Property(property="dni", type="string"),
 *     @OA\Property(property="cuit", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="Client",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person")
 * )
 *
 * @OA\Schema(
 *     schema="ClientRequest",
 *     required={"person"},
 *     @OA\Property(
 *         property="person",
 *         type="object",
 *         required={"name", "last_name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="last_name", type="string"),
 *         @OA\Property(property="address", type="string"),
 *         @OA\Property(property="phone_number", type="string"),
 *         @OA\Property(property="mail", type="string"),
 *         @OA\Property(property="dni", type="string"),
 *         @OA\Property(property="cuit", type="string")
 *     )
 * )
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
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Get all clients",
     *     tags={"Clients"},
     *     @OA\Response(
     *         response=200,
     *         description="List of clients retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Client")
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index(int $page): JsonResponse
    {
        $result = $this->service->getAll($page);

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
     * @OA\Get(
     *     path="/api/v1/clients/{id}",
     *     summary="Get client by ID",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client found",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/v1/clients",
     *     summary="Create a new client",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClientRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     )
     * )
     */
    public function store(ClientRequest $request): JsonResponse
    {

        $clientDTO = new ClientDTO(
            null,
            $request->input('balance', 0),
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
 * @OA\Put(
 *     path="/api/v1/clients/{id}",
 *     summary="Update an existing client",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="last_name", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="phone_number", type="string"),
 *             @OA\Property(property="mail", type="string"),
 *             @OA\Property(property="dni", type="string"),
 *             @OA\Property(property="cuit", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Client")
 *     )
 * )
 */
    public function update(int $id, PersonUpdateRequest $request): JsonResponse
    {
         $clientDTO = new ClientDTO(
            $id,
            null,
            new PersonDTO(
                $id,
                $request->input('name'),
                $request->input('last_name'),
                $request->input('address'),
                $request->input('phone_number'),
                $request->input('mail'),
                $request->input('dni'),
                $request->input('cuit')
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
     * @OA\Delete(
     *     path="/api/v1/clients/{id}",
     *     summary="Delete a client",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Client deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
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
