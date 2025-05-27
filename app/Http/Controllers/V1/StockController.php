<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\MaterialDTO;
use App\Services\V1\StockService;
use App\DTOs\V1\StockDTO;
use App\Http\Requests\V1\StockRequest;
use App\Http\Resources\V1\StockResource;
use App\Http\Resources\V1\StockResourceCollection;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Stocks",
 *     description="API Endpoints for Stock operations"
 * )
 *
 * @OA\Schema(
 *     schema="Stock",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="stock", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="material_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="StockRequest",
 *     required={"stock", "date", "material_id"},
 *     @OA\Property(property="stock", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="material_id", type="integer")
 * )
 */
class StockController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var StockService Service for stock data logic
     */
    protected StockService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param StockService $service
     */
    public function __construct(StockService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stocks",
     *     summary="Get all stocks",
     *     tags={"Stocks"},
     *     @OA\Response(
     *         response=200,
     *         description="List of stocks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Stock")
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
            new StockResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stocks/{id}",
     *     summary="Get stock by ID",
     *     tags={"Stocks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Stock ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock found",
     *         @OA\JsonContent(ref="#/components/schemas/Stock")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stock not found"
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
            new StockResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stocks",
     *     summary="Create a new stock record",
     *     tags={"Stocks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StockRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Stock created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Stock")
     *     )
     * )
     */
    public function store(StockRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $stockDTO = new StockDTO(
        null,
        $request->input('stock'),
        new Carbon($request->input('date')),
        new MaterialDTO(id: $request->input('material_id')),
    );

    $result = $this->service->create($stockDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new StockResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/stocks/{id}",
     *     summary="Update an existing stock record",
     *     tags={"Stocks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StockRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Stock")
     *     )
     * )
     */
    public function update(int $id,StockRequest $request): JsonResponse
    {
        $stockDTO = new StockDTO(
            $id,
            $request->input('stock'),
            new Carbon($request->input('date')),
            new MaterialDTO(id: $request->input('material_id')),
        );    $stockDTO = new StockDTO($id);
        $result = $this->service->update($stockDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new StockResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/stocks/{id}",
     *     summary="Delete a stock record",
     *     tags={"Stocks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Stock deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stock not found"
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
