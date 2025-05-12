<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeRepository
 *
 * Repository class for handling Employee CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class EmployeeRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Employees
     *
     * @return Collection Collection of Employee models
     */
    public function all(): Collection
    {
        return Employee::all();
    }

    /**
     * Find a Employee by ID
     *
     * @param int $id Employee ID to find
     * @return Employee|JsonResponse Found Employee model or error response
     * @throws Exception When Employee is not found
     */
    public function find(int $id): Employee|JsonResponse
    {
        $model = Employee::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Employee
     *
     * @param FormRequest $data Request containing Employee data
     * @return Employee Newly created Employee model
     */
    public function create(FormRequest $data): Employee
    {
        $data->validated();
        $model = Employee::create($data->all());
        return $model;
    }

    /**
     * Update an existing Employee
     *
     * @param int $id Employee ID to update
     * @param FormRequest $data Request containing updated Employee data
     * @return Employee|JsonResponse
     */
    public function update(int $id, FormRequest $data): Employee|JsonResponse
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
     * Delete a Employee
     *
     * @param int $id Employee ID to delete
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
