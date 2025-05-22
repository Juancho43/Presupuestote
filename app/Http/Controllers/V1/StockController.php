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
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stock Controller
 *
 * Handles HTTP requests related to stock records including CRUD operations
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
     * Get all stock records
     *
     * @return JsonResponse Collection of stock records
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
     * Get single stock record by ID
     *
     * @param int $id Stock record ID
     * @return JsonResponse Single stock resource
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
     * Create new stock record
     *
     * @param StockRequest $request Validated Stock data
     * @return JsonResponse Created stock resource
     */
    public function store(StockRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $stockDTO = new StockDTO(
        null,
        new Decimal($request->input('stock')),
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
     * Update existing stock record
     *
     * @param StockRequest $request Validated Stock data
     * @return JsonResponse Updated stock resource
     */
    public function update(int $id,StockRequest $request): JsonResponse
    {
        $stockDTO = new StockDTO(
            $id,
            new Decimal($request->input('stock')),
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
     * Delete stock record
     *
     * @param int $id Stock record ID
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
