<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\MeasureDTO;
use App\DTOs\V1\SubCategoryDTO;
use App\Services\V1\MaterialService;
use App\DTOs\V1\MaterialDTO;
use App\Http\Requests\V1\MaterialRequest;
use App\Http\Resources\V1\MaterialResource;
use App\Http\Resources\V1\MaterialResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Material Controller
 *
 * Handles HTTP requests related to material records including CRUD operations
 */
class MaterialController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var MaterialService Service for material data logic
     */
    protected MaterialService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param MaterialService $service
     */
    public function __construct(MaterialService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all material records
     *
     * @return JsonResponse Collection of material records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single material record by ID
     *
     * @param int $id Material record ID
     * @return JsonResponse Single material resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new material record
     *
     * @param MaterialRequest $request Validated Material data
     * @return JsonResponse Created material resource
     */
    public function store(MaterialRequest $request): JsonResponse
    {

        $materialDTO = new MaterialDTO(
            null,
            $request->input('name'),
            $request->input('description'),
            $request->input('brand'),
            $request->input('color'),
            new SubCategoryDTO(id:$request->input('sub_category_id')),
            new MeasureDTO(id: $request->input('measure_id')),
        );

        $result = $this->service->create($materialDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing material record
     *
     * @param MaterialRequest $request Validated Material data
     * @return JsonResponse Updated material resource
     */
    public function update(int $id,MaterialRequest $request): JsonResponse
    {
        $materialDTO = new MaterialDTO(
            $id,
            $request->input('name'),
            $request->input('description'),
            $request->input('brand'),
            $request->input('color'),
            new SubCategoryDTO(id:$request->input('sub_category_id')),
            new MeasureDTO(id: $request->input('measure_id')),
        );
        $result = $this->service->update($materialDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete material record
     *
     * @param int $id Material record ID
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
