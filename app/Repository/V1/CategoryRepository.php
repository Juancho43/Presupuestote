<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryRepository
 *
 * Repository class for handling Category CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class CategoryRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Categorys
     *
     * @return Collection Collection of Category models
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find a Category by ID
     *
     * @param int $id Category ID to find
     * @return Category|JsonResponse Found Category model or error response
     * @throws Exception When Category is not found
     */
    public function find(int $id): Category|JsonResponse
    {
        $model = Category::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Category
     *
     * @param FormRequest $data Request containing Category data
     * @return Category Newly created Category model
     */
    public function create(FormRequest $data): Category
    {
        $data->validated();
        $model = Category::create([
            'name' => $data->name,
        ]);
        return $model;
    }

    /**
     * Update an existing Category
     *
     * @param int $id Category ID to update
     * @param FormRequest $data Request containing updated Category data
     * @return Category|JsonResponse
     */
    public function update(int $id, FormRequest $data): Category|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                [
                    'name' => $data->name,
                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Category
     *
     * @param int $id Category ID to delete
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
