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
 * Price Controller
 *
 * Handles HTTP requests related to price records including CRUD operations
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
     * Get all price records
     *
     * @return JsonResponse Collection of price records
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
     * Get single price record by ID
     *
     * @param int $id Price record ID
     * @return JsonResponse Single price resource
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
     * Create new price record
     *
     * @param PriceRequest $request Validated Price data
     * @return JsonResponse Created price resource
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
     * Update existing price record
     *
     * @param PriceRequest $request Validated Price data
     * @return JsonResponse Updated price resource
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
     * Delete price record
     *
     * @param int $id Price record ID
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
