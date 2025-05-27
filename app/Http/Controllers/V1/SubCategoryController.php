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
 * @OA\Tag(
 *     name="SubCategories",
 *     description="API Endpoints for SubCategory operations"
 * )
 *
 * @OA\Schema(
 *     schema="SubCategory",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="category_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="SubCategoryRequest",
 *     required={"name", "category_id"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="category_id", type="integer")
 * )
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
     * @OA\Get(
     *     path="/api/v1/subcategories",
     *     summary="Get all subcategories",
     *     tags={"SubCategories"},
     *     @OA\Response(
     *         response=200,
     *         description="List of subcategories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/SubCategory")
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
            new SubCategoryResourceCollection($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Get subcategory by ID",
     *     tags={"SubCategories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="SubCategory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategory found",
     *         @OA\JsonContent(ref="#/components/schemas/SubCategory")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SubCategory not found"
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
            new SubCategoryResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/subcategories",
     *     summary="Create a new subcategory",
     *     tags={"SubCategories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SubCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="SubCategory created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubCategory")
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Update an existing subcategory",
     *     tags={"SubCategories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SubCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategory updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SubCategory")
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/v1/subcategories/{id}",
     *     summary="Delete a subcategory",
     *     tags={"SubCategories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="SubCategory deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SubCategory not found"
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
