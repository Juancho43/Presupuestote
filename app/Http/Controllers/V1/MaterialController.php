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
 * @OA\Tag(
 *     name="Materials",
 *     description="API Endpoints for Material operations"
 * )
 *
 * @OA\Schema(
 *     schema="Material",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="brand", type="string"),
 *     @OA\Property(property="color", type="string"),
 *     @OA\Property(property="sub_category_id", type="integer"),
 *     @OA\Property(property="measure_id", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="MaterialRequest",
 *     required={"name", "sub_category_id", "measure_id"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="brand", type="string"),
 *     @OA\Property(property="color", type="string"),
 *     @OA\Property(property="sub_category_id", type="integer"),
 *     @OA\Property(property="measure_id", type="integer")
 * )
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
     * @OA\Get(
     *     path="/api/v1/materials",
     *     summary="Get all materials",
     *     tags={"Materials"},
     *     @OA\Response(
     *         response=200,
     *         description="List of materials retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Material")
     *             ),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index(int $page): JsonResponse
    {
        $result = $this->service->getAll($page);

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
     * @OA\Get(
     *     path="/api/v1/materials/{id}",
     *     summary="Get material by ID",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Material ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material found",
     *         @OA\JsonContent(ref="#/components/schemas/Material")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
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
            new MaterialResource($result),
            "Data retrieved successfully",
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/materials",
     *     summary="Create a new material",
     *     tags={"Materials"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MaterialRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Material created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Material")
     *     )
     * )
     */
    public function store(MaterialRequest $request): JsonResponse
    {

        $materialDTO = new MaterialDTO(
            null,
            $request->input('name'),
            $request->input('description'),
            $request->input('color'),
            $request->input('brand'),
            $request->input('unit_measure'),
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
     * @OA\Put(
     *     path="/api/v1/materials/{id}",
     *     summary="Update an existing material",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MaterialRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Material")
     *     )
     * )
     */
    public function update(int $id,MaterialRequest $request): JsonResponse
    {
        $materialDTO = new MaterialDTO(
            $id,
            $request->input('name'),
            $request->input('description'),
            $request->input('color'),
            $request->input('brand'),
            $request->input('unit_measure'),
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
     * @OA\Delete(
     *     path="/api/v1/materials/{id}",
     *     summary="Delete a material",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Material deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
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
    /**
     * @OA\Get(
     *     path="/api/v1/materials/invoices/{id}",
     *     summary="Get material with related invoices",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Material ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material with invoices retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Material"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
     *     )
     * )
     */
    public function getWithInvoices(int $id): JsonResponse
    {
        $result = $this->service->getWithInvoices($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data retrieved successfully",
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/materials/works/{id}",
     *     summary="Get material with related works",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Material ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material with works retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Material"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
     *     )
     * )
     */
    public function getWithWorks(int $id): JsonResponse
    {
        $result = $this->service->getWithWorks($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data retrieved successfully",
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/materials/prices/{id}",
     *     summary="Get material with related prices",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Material ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material with prices retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Material"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
     *     )
     * )
     */
    public function getWithPrices(int $id): JsonResponse
    {
        $result = $this->service->getWithPrices($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data retrieved successfully",
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/materials/stocks/{id}",
     *     summary="Get material with related stocks",
     *     tags={"Materials"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Material ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Material with stocks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Material"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Material not found"
     *     )
     * )
     */
    public function getWithStocks(int $id): JsonResponse
    {
        $result = $this->service->getWithStocks($id);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            new MaterialResource($result),
            "Data retrieved successfully",
        );
    }

    public function search(string $query): JsonResponse
    {
        try {
            $result = $this->service->search($query);

            if ($result instanceof JsonResponse) {
                return $result;
            }

            return $this->successResponse(
                new MaterialResourceCollection($result),
                "Search results retrieved successfully",
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                "An error occurred while searching: " . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
