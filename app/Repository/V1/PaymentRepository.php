<?php

namespace App\Repository\V1;

use App\DTOs\V1\PaymentDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Salary;
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
            throw new Exception("Repository Error: can't find the resource with id: " . $id);
        }
        return $model;
    }

    /**
     * Create a new Payment
     *
     * @param PaymentDTO $data Request containing Payment data
     * @return Payment Newly created Payment model
     */
    public function create($data): Payment
    {
        $model = Payment::create([
            'payable_id' => $data->payable_id,
            'payable_type' => $data->payable_type,
            'amount' => $data->amount,
            'date' => $data->date,
            'description' => $data->description,
        ]);
        return $model;
    }

    /**
     * Update an existing Payment
     *
     * @param int $id Payment ID to update
     * @param PaymentDTO $data Request containing updated Payment data
     * @return Payment|JsonResponse
     */
    public function update(int $id,$data): Payment|JsonResponse
    {
        try {
            $model = $this->find($id)->update(
                [
                    'payable_id' => $data->payable_id,
                    'payable_type' => $data->payable_type,
                    'amount' => $data->amount,
                    'date' => $data->date,
                    'description' => $data->description,
                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse("Repository Error: can't update the resource", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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

    public function allClientPayments(int $clientId): Collection
    {
        return
            Payment::whereHasMorph('payable', [Budget::class], function ($query) use ($clientId) {
                $query->where('client_id', $clientId)->select(['id', 'description']);
            })->with('payable:id,description')->findOrFail($clientId);
    }
    public function allSupplierPayments(int $supplierId): Collection
    {
        return
            Payment::whereHasMorph('payable', [Invoice::class], function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId)->select(['id', 'description']);
            })->with('payable:id,date')->findOrFail($supplierId);
    }

    public function allEmployeePayments(int $employeeId): Collection
    {
        return Payment::whereHasMorph('payable', [Salary::class], function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)->select(['id', 'description']);
        })->with('payable:id,date')->findOrFail($employeeId);
    }
}
