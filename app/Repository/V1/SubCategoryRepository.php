<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SubCategoryRepository
 *
 * Repository class for handling SubCategory CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class SubCategoryRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all SubCategorys
     *
     * @return Collection Collection of SubCategory models
     */
    public function all(): Collection
    {
        return SubCategory::all();
    }

    /**
     * Find a SubCategory by ID
     *
     * @param int $id SubCategory ID to find
     * @return SubCategory|JsonResponse Found SubCategory model or error response
     * @throws Exception When SubCategory is not found
     */
    public function find(int $id): SubCategory|JsonResponse
    {
        $model = SubCategory::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new SubCategory
     *
     * @param FormRequest $data Request containing SubCategory data
     * @return SubCategory Newly created SubCategory model
     */
    public function create(FormRequest $data): SubCategory
    {
        $data->validated();
        $model = SubCategory::create($data->all());
        return $model;
    }

    /**
     * Update an existing SubCategory
     *
     * @param int $id SubCategory ID to update
     * @param FormRequest $data Request containing updated SubCategory data
     * @return SubCategory|JsonResponse
     */
    public function update(int $id, FormRequest $data): SubCategory|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                $data->all()
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a SubCategory
     *
     * @param int $id SubCategory ID to delete
     * @return bool|JsonResponse True if deleted successfully, error response otherwise
     */
    public function delete(int $id): bool|JsonResponse
    {
        try {
            return $this->find($id)->delete();
        } catch (Exception $e) {
            return $this->errorResponse('Error to delete the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
