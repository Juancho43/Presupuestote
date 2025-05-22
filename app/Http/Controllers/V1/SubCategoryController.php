<?php
namespace App\Http\Controllers\V1;

use App\DTOs\V1\CategoryDTO;
use App\Services\V1\SubCategoryService;
use App\DTOs\V1\SubCategoryDTO;
use App\Http\Requests\V1\SubCategoryRequest;
use App\Http\Resources\V1\SubCategoryResource;
use App\Http\Resources\V1\SubCategoryResourceCollection;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * SubCategory Controller
 *
 * Handles HTTP requests related to subcategory records including CRUD operations
 */
class SubCategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var SubCategoryService Service for subcategory data logic
     */
    protected SubCategoryService $service;

    /**
     * Initialize controller with service dependency
     *
     * @param SubCategoryService $service
     */
    public function __construct(SubCategoryService $service)
    {
        $this->service = $service->getInstance();
    }

    /**
     * Get all subcategory records
     *
     * @return JsonResponse Collection of subcategory records
     */
    public function index(): JsonResponse
    {
        $result = $this->service->getAll();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SubCategoryResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Get single subcategory record by ID
     *
     * @param int $id SubCategory record ID
     * @return JsonResponse Single subcategory resource
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->get($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SubCategoryResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Create new subcategory record
     *
     * @param SubCategoryRequest $request Validated SubCategory data
     * @return JsonResponse Created subcategory resource
     */
    public function store(SubCategoryRequest $request): JsonResponse
    {
    // Transform request data into DTO
    $subcategoryDTO = new SubCategoryDTO(
        null,
        $request->input('name'),
        new CategoryDTO(id: $request->input('category_id')),
    );

    $result = $this->service->create($subcategoryDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SubCategoryResource($result),
            "Data stored successfully",
            Response::HTTP_CREATED
        );
    }

    /**
     * Update existing subcategory record
     *
     * @param SubCategoryRequest $request Validated SubCategory data
     * @return JsonResponse Updated subcategory resource
     */
    public function update(int $id,SubCategoryRequest $request): JsonResponse
    {
        $subcategoryDTO = new SubCategoryDTO(
        $id,
        $request->input('name'),
        new CategoryDTO(id: $request->input('category_id')),
        );
        $result = $this->service->update($subcategoryDTO);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new SubCategoryResource($result),
            "Data updated successfully",
            Response::HTTP_OK
        );
    }

    /**
     * Delete subcategory record
     *
     * @param int $id SubCategory record ID
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
