<?php
namespace App\Repository\V1;

use App\Models\Stock;
use App\DTOs\V1\StockDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class StockRepository implements IRepository
{
    /**
     * Get all Stocks
     *
     * @return Collection Collection of Stock models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Stock::all();
    }

    /**
     * Find a Stock by ID
     *
     * @param int $id Stock ID to find
     * @return Stock Found Stock model
     * @throws Exception When Stock is not found
     */
    public function find(int $id): Model
    {
        $model = Stock::where('id', $id)->first();
        if (!$model) {
            throw new Exception("Stock with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Stock
     *
     * @param StockDTO $data DTO containing Stock data
     * @return Stock Newly created Stock
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Stock::create([
            'quantity' => $data->stock,
            'material_id' => $data->material->id,
            'date' => $data->date,
        ]);
    }

    /**
     * Update an existing Stock
     *
     * @param StockDTO $data DTO containing updated Stock data
     * @return Stock Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'quantity' => $data->stock,
            'material_id' => $data->material->id,
            'date' => $data->date,
        ])) {
            throw new Exception("Failed to update Stock: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Stock
     *
     * @param int $id Stock ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
