<?php
namespace App\Repository\V1;

use App\Models\Work;
use App\DTOs\V1\WorkDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class WorkRepository implements IRepository
{
    /**
     * Get all Works
     *
     * @return Collection Collection of Work models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Work::with('budget')->get();
    }

    /**
     * Find a Work by ID
     *
     * @param int $id Work ID to find
     * @return Work Found Work model
     * @throws Exception When Work is not found
     */
    public function find(int $id): Model
    {
        $model = Work::with(['materials','materials.measure','budget'])->find($id);
        if (!$model) {
            throw new Exception("Work with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Work
     *
     * @param WorkDTO $data DTO containing Work data
     * @return Work Newly created Work
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Work::create([
            'order' => $data->order,
            'name' => $data->name,
            'notes' => $data->notes,
            'estimated_time' => $data->estimated_time,
            'dead_line' => $data->dead_line,
            'status' => $data->cost ?? 0,
            'budget_id' => $data->budget->id,
        ]);
    }

    /**
     * Update an existing Work
     *
     * @param WorkDTO $data DTO containing updated Work data
     * @return Work Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'order' => $data->order,
            'name' => $data->name,
            'notes' => $data->notes,
            'estimated_time' => $data->estimated_time,
            'dead_line' => $data->dead_line,
            'status' => $data->cost ?? 0,
            'budget_id' => $data->budget->id,
        ])) {
            throw new Exception("Failed to update Work: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Work
     *
     * @param int $id Work ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
