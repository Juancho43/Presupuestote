<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BudgetRepository
 *
 * Repository class for handling Budget CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class BudgetRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Budgets
     *
     * @return Collection Collection of Budget models
     */
    public function all(): Collection
    {
        return Budget::all();
    }

    /**
     * Find a Budget by ID
     *
     * @param int $id Budget ID to find
     * @return Budget|JsonResponse Found Budget model or error response
     * @throws Exception When Budget is not found
     */
    public function find(int $id): Budget|JsonResponse
    {
        $model = Budget::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Budget
     *
     * @param FormRequest $data Request containing Budget data
     * @return Budget Newly created Budget model
     */
    public function create(FormRequest $data): Budget
    {
        $data->validated();
        $model = Budget::create($data->all());
        return $model;
    }

    /**
     * Update an existing Budget
     *
     * @param int $id Budget ID to update
     * @param FormRequest $data Request containing updated Budget data
     * @return Budget|JsonResponse
     */
    public function update(int $id, FormRequest $data): Budget|JsonResponse
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
     * Delete a Budget
     *
     * @param int $id Budget ID to delete
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
