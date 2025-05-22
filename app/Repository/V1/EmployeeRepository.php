<?php
namespace App\Repository\V1;

use App\DTOs\V1\EmployeeDTO;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class EmployeeRepository implements IRepository
{
    /**
     * Get all Employees
     *
     * @return Collection Collection of Employee models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Employee::with('person')->get();
    }

    /**
     * Find a Employee by ID
     *
     * @param int $id Employee ID to find
     * @return Employee Found Employee model
     * @throws Exception When Employee is not found
     */
    public function find(int $id): Model
    {
        $model = Employee::with(['person','invoices'])->where('id', $id)->first();
        if (!$model) {
            throw new Exception("Employee with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Employee
     *
     * @param EmployeeDTO $data DTO containing Employee data
     * @return Employee Newly created Employee
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Employee::create([
            'salary' => $data->salary,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'is_active' => $data->isActive,
            'person_id' => $data->person->id,
        ]);
    }

    /**
     * Update an existing Employee
     *
     * @param EmployeeDTO $data DTO containing updated Employee data
     * @return Employee Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'salary' => $data->salary ?? $model->salary ,
            'start_date' => $data->startDate ?? $model->startDate,
            'end_date' => $data->endDate ?? $model->endDate,
            'is_active' => $data->isActive ?? $model->isActive,
        ])) {
            throw new Exception("Failed to update Employee: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Employee
     *
     * @param int $id Employee ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
