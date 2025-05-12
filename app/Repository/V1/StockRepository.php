<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StockRepository
 *
 * Repository class for handling Stock CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class StockRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Stocks
     *
     * @return Collection Collection of Stock models
     */
    public function all(): Collection
    {
        return Stock::all();
    }

    /**
     * Find a Stock by ID
     *
     * @param int $id Stock ID to find
     * @return Stock|JsonResponse Found Stock model or error response
     * @throws Exception When Stock is not found
     */
    public function find(int $id): Stock|JsonResponse
    {
        $model = Stock::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Stock
     *
     * @param FormRequest $data Request containing Stock data
     * @return Stock Newly created Stock model
     */
    public function create(FormRequest $data): Stock
    {
        $data->validated();
        $model = Stock::create($data->all());
        return $model;
    }

    /**
     * Update an existing Stock
     *
     * @param int $id Stock ID to update
     * @param FormRequest $data Request containing updated Stock data
     * @return Stock|JsonResponse
     */
    public function update(int $id, FormRequest $data): Stock|JsonResponse
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
     * Delete a Stock
     *
     * @param int $id Stock ID to delete
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
