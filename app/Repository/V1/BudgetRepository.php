<?php

namespace App\Repository\V1;

use App\DTOs\V1\BudgetDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Budget;
use App\Models\Work;
use App\States\BudgetState\Aprobado;
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
material */
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
        return Budget::with('client.person')->get();
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
        try {
            $model = Budget::with([
                'works' => function ($query) {
                    $query->with(['materials' => function ($query) {
                        $query->select('materials.id', 'materials.name', 'materials.description', 'materials.color', 'materials.brand', 'material_work.quantity');
                    }]);
                },
                'payments'
            ])->findOrFail($id);
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error retrieving data', $e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new Budget
     *
     * @param BudgetDTO $data Request containing Budget data
     * @return Budget Newly created Budget model
     */
    public function create($data): Budget
    {
        $model = Budget::create([
            'made_date' => $data->made_date,
            'description' => $data->description,
            'dead_line' => $data->dead_line,
            'profit' => $data->profit ?? 0,
            'price' => $data->price ?? 0,
            'cost' => $data->cost ?? 0,
            'client_id' => $data->client_id
        ]);
        return $model;
    }

    /**
     * Update an existing Budget
     *
     * @param int $id Budget ID to update
     * @param BudgetDTO $data Request containing updated Budget data
     * @return Budget|JsonResponse
     */
    public function update(int $id,$data): Budget|JsonResponse
    {
        try {
            $model = $this->find($id)->update(
                [
                    'made_date' => $data->made_date,
                    'description' => $data->description,
                    'dead_line' => $data->dead_line,
                    'profit' => $data->profit ?? 0,
                    'price' => $data->price ?? 0,
                    'cost' => $data->cost ?? 0,
                    'client_id' => $data->client_id
                ]
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

   public function addWorks(int $budgetId, array $workIds): Budget | JsonResponse
   {
       try {

           $budget = Budget::findOrFail($budgetId);
           Work::whereIn('id', $workIds)->update(['budget_id' => $budgetId]);
           return $budget->fresh('works');
       }catch (Exception $e ){
           return $this->errorResponse('Error adding works to budget', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }

   }
}
