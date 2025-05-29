<?php
namespace App\Repository\V1;

use App\Models\Salary;
use App\DTOs\V1\SalaryDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class SalaryRepository implements IRepository
{
    /**
     * Get all Salarys
     *
     * @return Collection Collection of Salary models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Salary::with('employee.person')->get();
    }

    /**
     * Find a Salary by ID
     *
     * @param int $id Salary ID to find
     * @return Salary Found Salary model
     * @throws Exception When Salary is not found
     */
    public function find(int $id): Model
    {
        $model = Salary::with(['payments' , 'employee.person'])->find($id);
        if (!$model) {
            throw new Exception("Salary with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Salary
     *
     * @param SalaryDTO $data DTO containing Salary data
     * @return Salary Newly created Salary
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Salary::create([
            'amount' => $data->amount,
            'date' => $data->date->format('Y-m-d'),
            'active' => $data->active,
            'employee_id' => $data->employee->id,
        ]);
    }

    /**
     * Update an existing Salary
     *
     * @param SalaryDTO $data DTO containing updated Salary data
     * @return Salary Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'amount' => $data->amount,
            'date' => $data->date->format('Y-m-d'),
            'active' => $data->active,
            'employee_id' => $data->employee->id,
        ])) {
            throw new Exception("Failed to update Salary: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Salary
     *
     * @param int $id Salary ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
