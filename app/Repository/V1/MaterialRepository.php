<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MaterialRepository
 *
 * Repository class for handling Material CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class MaterialRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Materials
     *
     * @return Collection Collection of Material models
     */
  public function all(): Collection
  {
     return Material::with([
        'measure',
        'subcategory',
        'prices' => function ($query) {
            $query->latest('date')->limit(1);
        },
        'stocks' => function ($query) {
            $query->latest('date')->limit(1);
        }
    ])->get();

  }

    /**
     * Find a Material by ID
     *
     * @param int $id Material ID to find
     * @return Material|JsonResponse Found Material model or error response
     * @throws Exception When Material is not found
     */
    public function find(int $id): Material|JsonResponse
    {
        $model = Material::with([
            'prices' => function($query) {
                $query->select('id', 'material_id', 'price', 'date');
            },
            'stocks' => function($query) {
                $query->select('id', 'material_id', 'stock', 'date');
            },
            'measure' => function($query) {
                $query->select('id', 'name');
            },
            'subcategory' => function($query) {
                $query->select('id', 'name', 'category_id')
                    ->with(['category' => function($query) {
                        $query->select('id', 'name');
                    }]);
            }
        ])->findOrFail($id);
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Material
     *
     * @param FormRequest $data Request containing Material data
     * @return Material Newly created Material model
     */
    public function create(FormRequest $data): Material
    {
        $data->validated();
        $model = Material::create($data->all());
        return $model;
    }

    /**
     * Update an existing Material
     *
     * @param int $id Material ID to update
     * @param FormRequest $data Request containing updated Material data
     * @return Material|JsonResponse
     */
    public function update(int $id, FormRequest $data): Material|JsonResponse
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
     * Delete a Material
     *
     * @param int $id Material ID to delete
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
