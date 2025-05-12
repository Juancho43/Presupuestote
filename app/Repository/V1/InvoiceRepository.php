<?php

namespace App\Repository\V1;

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
        return Invoice::all();
    }

    /**
     * Find a Invoice by ID
     *
     * @param int $id Invoice ID to find
     * @return Invoice|JsonResponse Found Invoice model or error response
     * @throws Exception When Invoice is not found
     */
    public function find(int $id): Invoice|JsonResponse
    {
        $model = Invoice::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Invoice
     *
     * @param FormRequest $data Request containing Invoice data
     * @return Invoice Newly created Invoice model
     */
    public function create(FormRequest $data): Invoice
    {
        $data->validated();
        $model = Invoice::create($data->all());
        return $model;
    }

    /**
     * Update an existing Invoice
     *
     * @param int $id Invoice ID to update
     * @param FormRequest $data Request containing updated Invoice data
     * @return Invoice|JsonResponse
     */
    public function update(int $id, FormRequest $data): Invoice|JsonResponse
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
}
