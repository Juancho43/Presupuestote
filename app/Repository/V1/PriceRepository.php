<?php

namespace App\Repository\V1;

use App\DTOs\V1\PriceDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Price;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PriceRepository
 *
 * Repository class for handling Price CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class PriceRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Prices
     *
     * @return Collection Collection of Price models
     */
    public function all(): Collection
    {
        return Price::all();
    }

    /**
     * Find a Price by ID
     *
     * @param int $id Price ID to find
     * @return Price|JsonResponse Found Price model or error response
     * @throws Exception When Price is not found
     */
    public function find(int $id): Price|JsonResponse
    {
        $model = Price::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Price
     *
     * @param PriceDTO $data Request containing Price data
     * @return Price Newly created Price model
     */
    public function create($data): Price

    {
        $date = $data->date->format('Y-m-d');
        $model = Price::create([
            'price' => $data->price,
            'date' => $date,
            'material_id' => $data->material->id,
        ]);
        return $model;
    }

    /**
     * Update an existing Price
     *
     * @param int $id Price ID to update
     * @param PriceDTO $data Request containing updated Price data
     * @return Price|JsonResponse
     */
    public function update(int $id, $data): Price|JsonResponse
    {
        try {
            $date = $data->date->format('Y-m-d');
            $model = $this->find($id)->update(
                [
                    'price' => $data->price,
                    'date' => $date,
                    'material_id' => $data->material->id,
                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Price
     *
     * @param int $id Price ID to delete
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
