<?php
namespace App\Repository\V1;

use App\DTOs\V1\BudgetDTO;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;
use Illuminate\Contracts\Pagination\Paginator;


class BudgetRepository implements IRepository
{
    /**
     * Get all Budgets
     *
     * @return Paginator Collection of Budget models
     * @throws Exception If database query fails
     */
    public function all(int $page = 1):Paginator
    {
        return Budget::with('client.person')->simplePaginate(getenv('PER_PAGE'),$page);
    }


    /**
     * Find a Budget by ID
     *
     * @param int $id Budget ID to find
     * @return Model Found Budget model
     * @throws Exception When Budget is not found
     */
    public function find(int $id): Model
    {
        $model = Budget::with([
            'works' => function ($query) {
                $query->with(['materials' => function ($query) {
                    $query->select('materials.id', 'materials.name', 'materials.description', 'materials.color', 'materials.brand', 'material_work.quantity');
                }]);
            },
            'payments',
            'client.person'
        ])->find($id);
        if (!$model) {
            throw new Exception("Budget with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Budget
     *
     * @param BudgetDTO $data DTO containing Budget data
     * @return Model Newly created Budget
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        $model = Budget::create([
        'made_date' => $data->madeDate,
        'description' => $data->description,
        'dead_line' => $data->deadLine,
        'profit' => $data->profit ?? 0,
        'price' => $data->price ?? 0,
        'cost' => $data->cost ?? 0,
        'client_id' => $data->client->id
        ]);
        return $model;
    }

    /**
     * Update an existing Budget
     *
     * @param BudgetDTO $data DTO containing updated Budget data
     * @return Model Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);

        if (!$model->update([
            'made_date' => $data->madeDate,
            'description' => $data->description,
            'dead_line' => $data->deadLine,
            'profit' => $data->profit ?? 0,
            'price' => $data->price ?? 0,
            'cost' => $data->cost ?? 0,
            'client_id' => $data->client->id
        ])) {
            throw new Exception("Failed to update Budget: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Budget
     *
     * @param int $id Budget ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function getAll(): Collection
    {
        return Budget::with('client.person')->get();
    }
}
