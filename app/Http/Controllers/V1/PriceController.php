<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\MaterialDTO;
use App\Services\V1\PriceService;
use App\DTOs\V1\PriceDTO;
use App\Http\Requests\V1\PriceRequest;
use App\Http\Resources\V1\PriceResource;
use App\Http\Resources\V1\PriceResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Prices",
 *     description="API Endpoints for Price operations"
 * )
 *
 * @OA\Schema(
 *     schema="Price",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="material_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="PriceRequest",
 *     required={"price", "date", "material_id"},
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="material_id", type="integer")
 * )
 */
class PriceController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var PriceService Service for price data logic
     */
    protected PriceService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param PriceService $service
     */
    public function __construct(PriceService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/prices",
     *     summary="Get all prices",
     *     tags={"Prices"},
     *     @OA\Response(
     *         response=200,
     *         description="List of prices retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Price")
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
            new PriceResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }


    /**
     * @OA\Get(
     *     path="/api/v1/prices/{id}",
     *     summary="Get price by ID",
     *     tags={"Prices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Price ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Price found",
     *         @OA\JsonContent(ref="#/components/schemas/Price")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Price not found"
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
            new PriceResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/prices",
     *     summary="Create a new price",
     *     tags={"Prices"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PriceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Price created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Price")
     *     )
     * )
     */
    public function store(PriceRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $priceDTO = new PriceDTO(
        null,
        $request->input('price'),
        new Carbon($request->input('date')),
        new MaterialDTO(id: $request->input('material_id')),
    );

    $result = $this->service->create($priceDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new PriceResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/prices/{id}",
     *     summary="Update an existing price",
     *     tags={"Prices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PriceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Price updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Price")
     *     )
     * )
     */
    public function update(int $id,PriceRequest $request): JsonResponse
    {
        $priceDTO = new PriceDTO(
            $id,
            $request->input('price'),
            new Carbon($request->input('date')),
            new MaterialDTO(id: $request->input('material_id')),
        );
        $result = $this->service->update($priceDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new PriceResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/prices/{id}",
     *     summary="Delete a price",
     *     tags={"Prices"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Price deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Price not found"
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
