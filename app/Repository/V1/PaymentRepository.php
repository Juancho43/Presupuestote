<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentRepository
 *
 * Repository class for handling Payment CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class PaymentRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Payments
     *
     * @return Collection Collection of Payment models
     */
    public function all(): Collection
    {
        return Payment::all();
    }

    /**
     * Find a Payment by ID
     *
     * @param int $id Payment ID to find
     * @return Payment|JsonResponse Found Payment model or error response
     * @throws Exception When Payment is not found
     */
    public function find(int $id): Payment|JsonResponse
    {
        $model = Payment::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Payment
     *
     * @param FormRequest $data Request containing Payment data
     * @return Payment Newly created Payment model
     */
    public function create(FormRequest $data): Payment
    {
        $data->validated();
        $model = Payment::create($data->all());
        return $model;
    }

    /**
     * Update an existing Payment
     *
     * @param int $id Payment ID to update
     * @param FormRequest $data Request containing updated Payment data
     * @return Payment|JsonResponse
     */
    public function update(int $id, FormRequest $data): Payment|JsonResponse
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
     * Delete a Payment
     *
     * @param int $id Payment ID to delete
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
