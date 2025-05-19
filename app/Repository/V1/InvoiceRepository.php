<?php

namespace App\Repository\V1;

use App\DTOs\V1\InvoiceDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvoiceRepository
 *
 * Repository class for handling Invoice CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class InvoiceRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Invoices
     *
     * @return Collection Collection of Invoice models
     */
    public function all(): Collection
    {
        return Invoice::with(['supplier.person'])->get();
    }

    /**
     * Find am Invoice by ID
     *
     * @param int $id Invoice ID to find
     * @return Invoice|JsonResponse Found Invoice model or error response
     * @throws Exception When Invoice is not found
     */
public function find(int $id): Invoice|JsonResponse
{
    $model = Invoice::with([
        'materials',
        'materials.prices' => function($query) {
            $query->select('id', 'material_id', 'price');
        },
        'supplier.person'
    ])->findOrFail($id);

    if (!$model) {
        throw new Exception('Error to find the resource with id: ' . $id);
    }
    return $model;
}
    /**
     * Create a new Invoice
     *
     * @param  InvoiceDTO $data Request containing Invoice data
     * @return Invoice Newly created Invoice model
     */
    public function create($data): Invoice
    {
        $model = Invoice::create([
            'supplier_id' => $data->supplier->id,
            'date' => $data->date,
        ]);
        return $model;
    }

    /**
     * Update an existing Invoice
     *
     * @param int $id Invoice ID to update
     * @param InvoiceDTO $data Request containing updated Invoice data
     * @return Invoice|JsonResponse
     */
    public function update(int $id,$data): Invoice|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                [
                    'supplier_id' => $data->supplier->id,
                    'date' => $data->date,
                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Invoice
     *
     * @param int $id Invoice ID to delete
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
    public function addMaterials(int $invoiceId, array $materialsIds, array $pricesId, array $quantities): Invoice|JsonResponse
    {
        try {
            $model = $this->find($invoiceId);
            $model->materials()->sync($materialsIds);
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to add materials to the invoice', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
