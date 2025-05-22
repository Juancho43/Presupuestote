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
 * Measure Controller
 *
 * Handles HTTP requests related to measure records including CRUD operations
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
     * Get all measure records
     *
     * @return JsonResponse Collection of measure records
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
     * Get single measure record by ID
     *
     * @param int $id Measure record ID
     * @return JsonResponse Single measure resource
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
     * Create new measure record
     *
     * @param MeasureRequest $request Validated Measure data
     * @return JsonResponse Created measure resource
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
     * Update existing measure record
     *
     * @param MeasureRequest $request Validated Measure data
     * @return JsonResponse Updated measure resource
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
     * Delete measure record
     *
     * @param int $id Measure record ID
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
