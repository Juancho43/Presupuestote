<?php
namespace App\Repository\V1;

use App\Models\Supplier;
use App\DTOs\V1\SupplierDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class SupplierRepository implements IRepository
{
    /**
     * Get all Suppliers
     *
     * @return Collection Collection of Supplier models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Supplier::with(['person'])->get();
    }

    /**
     * Find a Supplier by ID
     *
     * @param int $id Supplier ID to find
     * @return Supplier Found Supplier model
     * @throws Exception When Supplier is not found
     */
    public function find(int $id): Model
    {
        $model = Supplier::with(['person','invoice'])->find($id);
        if (!$model) {
            throw new Exception("Supplier with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Supplier
     *
     * @param SupplierDTO $data DTO containing Supplier data
     * @return Supplier Newly created Supplier
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Supplier::create([
            'balance' => $data->balance,
            'notes' => $data->notes,
            'person_id' => $data->person->id,
        ]);
    }

    /**
     * Update an existing Supplier
     *
     * @param SupplierDTO $data DTO containing updated Supplier data
     * @return Supplier Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'balance' => $data->balance ?? $model->balance,
            'notes' => $data->notes ?? $model->notes,
        ])) {
            throw new Exception("Failed to update Supplier: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Supplier
     *
     * @param int $id Supplier ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
