<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Salary;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SalaryRepository
 *
 * Repository class for handling Salary CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class SalaryRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Salarys
     *
     * @return Collection Collection of Salary models
     */
    public function all(): Collection
    {
        return Salary::with(['employee.person'])->get();
    }

    /**
     * Find a Salary by ID
     *
     * @param int $id Salary ID to find
     * @return Salary|JsonResponse Found Salary model or error response
     * @throws Exception When Salary is not found
     */
    public function find(int $id): Salary|JsonResponse
    {
        try {
            $model = Salary::with([
                'payments'
            ])->findOrFail($id);

            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error retrieving data', $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new Salary
     *
     * @param FormRequest $data Request containing Salary data
     * @return Salary Newly created Salary model
     */
    public function create(FormRequest $data): Salary
    {
        $data->validated();
        $model = Salary::create([
            'amount' => $data->input('amount'),
            'date' => $data->input('date'),
            'active' => $data->input('active'),
            'employee_id' => $data->input('employee_id'),
        ]);
        return $model;
    }

    /**
     * Update an existing Salary
     *
     * @param int $id Salary ID to update
     * @param FormRequest $data Request containing updated Salary data
     * @return Salary|JsonResponse
     */
    public function update(int $id, FormRequest $data): Salary|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                [
                    'amount' => $data->input('amount'),
                    'date' => $data->input('date'),
                    'active' => $data->input('active'),
                    'employee_id' => $data->input('employee_id'),
                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Salary
     *
     * @param int $id Salary ID to delete
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
