<?php
namespace App\Http\Controllers\V1;

use App\Services\V1\MeasureService;
use App\DTOs\V1\MeasureDTO;
use App\Http\Requests\V1\MeasureRequest;
use App\Http\Resources\V1\MeasureResource;
use App\Http\Resources\V1\MeasureResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Measures",
 *     description="API Endpoints for Measure operations"
 * )
 *
 * @OA\Schema(
 *     schema="Measure",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="MeasureRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string")
 * )
 */
class MeasureController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var MeasureService Service for measure data logic
     */
    protected MeasureService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param MeasureService $service
     */
    public function __construct(MeasureService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/measures",
     *     summary="Get all measures",
     *     tags={"Measures"},
     *     @OA\Response(
     *         response=200,
     *         description="List of measures retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Measure")
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
            new MeasureResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/measures/{id}",
     *     summary="Get measure by ID",
     *     tags={"Measures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Measure ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Measure found",
     *         @OA\JsonContent(ref="#/components/schemas/Measure")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Measure not found"
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
            new MeasureResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/measures",
     *     summary="Create a new measure",
     *     tags={"Measures"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MeasureRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Measure created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Measure")
     *     )
     * )
     */
    public function store(MeasureRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $measureDTO = new MeasureDTO(
        null,
        $request->input('name'),
        $request->input('description'),
    );

    $result = $this->service->create($measureDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MeasureResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/measures/{id}",
     *     summary="Update an existing measure",
     *     tags={"Measures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MeasureRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Measure updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Measure")
     *     )
     * )
     */
    public function update(int $id,MeasureRequest $request): JsonResponse
    {
        $measureDTO = new MeasureDTO(
            $id,
            $request->input('name'),
            $request->input('description'),
        );
        $result = $this->service->update($measureDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MeasureResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/measures/{id}",
     *     summary="Delete a measure",
     *     tags={"Measures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Measure deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Measure not found"
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
