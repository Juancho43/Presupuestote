<?php
namespace App\Repository\V1;

use App\Models\Price;
use App\DTOs\V1\PriceDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class PriceRepository implements IRepository
{
    /**
     * Get all Prices
     *
     * @return Collection Collection of Price models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Price::all();
    }

    /**
     * Find a Price by ID
     *
     * @param int $id Price ID to find
     * @return Price Found Price model
     * @throws Exception When Price is not found
     */
    public function find(int $id): Model
    {
        $model = Price::with('material')->find($id);
        if (!$model) {
            throw new Exception("Price with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Price
     *
     * @param PriceDTO $data DTO containing Price data
     * @return Price Newly created Price
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        $date = $data->date->format('Y-m-d');
        return Price::create([
            'price' => $data->price,
            'date' => $date,
            'material_id' => $data->material->id,
        ]);

    }

    /**
     * Update an existing Price
     *
     * @param PriceDTO $data DTO containing updated Price data
     * @return Price Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        $date = $data->date->format('Y-m-d');
        if (!$model->update([
            'price' => $data->price,
            'date' => $date,
            'material_id' => $data->material->id,
        ])) {
            throw new Exception("Failed to update Price: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Price
     *
     * @param int $id Price ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
