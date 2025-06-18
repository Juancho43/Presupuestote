<?php
namespace App\Repository\V1;

use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Payment;
use App\DTOs\V1\PaymentDTO;
use App\Models\Salary;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class PaymentRepository implements IRepository
{
    /**
     * Get all Payments
     *
     * @return Collection Collection of Payment models
     * @throws Exception If database query fails
     */
    public function all(int $page = 1):Paginator
    {
        return Payment::simplePaginate(getenv('PER_PAGE'),page:$page);
    }

    /**
     * Find a Payment by ID
     *
     * @param int $id Payment ID to find
     * @return Payment Found Payment model
     * @throws Exception When Payment is not found
     */
    public function find(int $id): Model
    {
        $model = Payment::where('id', $id)->first();
        if (!$model) {
            throw new Exception("Payment with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Payment
     *
     * @param PaymentDTO $data DTO containing Payment data
     * @return Payment Newly created Payment
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Payment::create([
            'payable_id' => $data->payable_id,
            'payable_type' => $data->payable_type,
            'amount' => $data->amount,
            'date' => $data->date,
            'description' => $data->description,
        ]);
    }

    /**
     * Update an existing Payment
     *
     * @param PaymentDTO $data DTO containing updated Payment data
     * @return Payment Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'payable_id' => $data->payable_id,
            'payable_type' => $data->payable_type,
            'amount' => $data->amount,
            'date' => $data->date,
            'description' => $data->description,
        ])) {
            throw new Exception("Failed to update Payment: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Payment
     *
     * @param int $id Payment ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
    public function allClientPayments(int $clientId, int $page): Paginator
    {
        return
          Payment::whereHasMorph('payable', [Budget::class], function ($query) use ($clientId) {
              $query->where('client_id', $clientId);
          })->with('payable:id,description')
              ->orderBy('date', 'desc')
              ->simplePaginate(getenv('PER_PAGE'),page:$page);
    }
    public function allSupplierPayments(int $supplierId , int $page): Paginator
    {
        return
          Payment::whereHasMorph('payable', [Invoice::class], function ($query) use ($supplierId) {
              $query->where('supplier_id', $supplierId);
          })->with('payable:id,date')
                ->orderBy('date', 'desc')
              ->simplePaginate(getenv('PER_PAGE'),page:$page);
    }

    public function allEmployeePayments(int $employeeId, int $page): Paginator
    {
        return Payment::whereHasMorph('payable', [Salary::class], function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })->with('payable:id,date')
                ->orderBy('date', 'desc')
            ->simplePaginate(getenv('PER_PAGE'),page:$page);
    }

    public function getAll(): Collection
    {
        return Payment::all();
    }

}
