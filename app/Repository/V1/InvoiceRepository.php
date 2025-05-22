<?php
namespace App\Repository\V1;

use App\Models\Invoice;
use App\DTOs\V1\InvoiceDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class InvoiceRepository implements IRepository
{
    /**
     * Get all Invoices
     *
     * @return Collection Collection of Invoice models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Invoice::with(['supplier.person'])->get();
    }

    /**
     * Find a Invoice by ID
     *
     * @param int $id Invoice ID to find
     * @return Invoice Found Invoice model
     * @throws Exception When Invoice is not found
     */
    public function find(int $id): Model
    {
        $model = Invoice::with([
            'materials',
            'materials.latestPrice',
            'supplier.person'
        ])->findOrFail($id);
        if (!$model) {
            throw new Exception("Invoice with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Invoice
     *
     * @param InvoiceDTO $data DTO containing Invoice data
     * @return Invoice Newly created Invoice
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Invoice::create([
            'supplier_id' => $data->supplier->id,
            'date' => $data->date,
        ]);
    }

    /**
     * Update an existing Invoice
     *
     * @param InvoiceDTO $data DTO containing updated Invoice data
     * @return Invoice Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'supplier_id' => $data->supplier->id,
            'date' => $data->date,
        ])) {
            throw new Exception("Failed to update Invoice: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Invoice
     *
     * @param int $id Invoice ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

}
