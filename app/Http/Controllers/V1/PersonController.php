<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\PersonDTO;
use App\Http\Requests\V1\PersonRequest;
use App\Http\Requests\V1\PersonUpdateRequest;
use App\Http\Resources\V1\IPersonResourceCollection;
use App\Http\Resources\V1\PersonResource;
use App\Http\Resources\V1\PersonResourceCollection;
use App\Services\V1\PersonService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Tag(
 *     name="People",
 *     description="Operations about people"
 * )
 *
 *
 * @OA\Schema(
 *     schema="PersonRequest",
 *     required={"name", "phone_number"},
 *     @OA\Property(property="name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="phone_number", type="string", example="555-1234"),
 *     @OA\Property(property="mail", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="cuit", type="string", example="20-12345678-9")
 * )
 */
class PersonController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PersonService Service for person data logic
     */
    protected PersonService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param PersonService $service
     */
    public function __construct(PersonService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people",
     *     summary="Get all people",
     *     tags={"People"},
     *     @OA\Response(
     *         response=200,
     *         description="List of people retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Person")
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new PersonResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }



    /**
     * @OA\Get(
     *     path="/api/v1/people/{id}",
     *     summary="Get person by ID",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Person ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person found",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
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
            new PersonResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/people",
     *     summary="Create a new person",
     *     tags={"People"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PersonRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Person created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     )
     * )
     */
    public function store(PersonRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $peopleDTO = new PersonDTO(
        null,
        $request->input('name'),
        $request->input('last_name'),
        $request->input('address'),
        $request->input('phone_number'),
        $request->input('mail'),
        $request->input('dni'),
        $request->input('cuit')

    );

    $result = $this->service->create($peopleDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new PersonResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/people/{id}",
     *     summary="Update an existing person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PersonRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person updated successfully",
     *        @OA\JsonContent(ref="#/components/schemas/Person")
     *     )
     * )
     */
    public function update(int $id,PersonUpdateRequest $request): JsonResponse
    {
        $peopleDTO = new PersonDTO(
            $id,
            $request->input('name'),
            $request->input('last_name'),
            $request->input('address'),
            $request->input('phone_number'),
            $request->input('mail'),
            $request->input('dni'),
            $request->input('cuit')

        );
        $result = $this->service->update($peopleDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new PersonResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }
    /**
    * @OA\Delete(
    *     path="/api/v1/people/{id}",
    *     summary="Delete a person",
    *     tags={"People"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=204,
    *         description="Person deleted successfully"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Person not found"
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

    public function search(string $entity, string $search): JsonResponse
    {
        try {
            $result = $this->service->search($entity, $search);
            return $this->successResponse(
                new IPersonResourceCollection($result),
                "Search results retrieved successfully",
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                "Error retrieving search results: " . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
