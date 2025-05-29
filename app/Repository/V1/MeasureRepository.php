<?php
namespace App\Repository\V1;

use App\Models\Measure;
use App\DTOs\V1\MeasureDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class MeasureRepository implements IRepository
{
    /**
     * Get all Measures
     *
     * @return Collection Collection of Measure models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Measure::all();
    }

    /**
     * Find a Measure by ID
     *
     * @param int $id Measure ID to find
     * @return Measure Found Measure model
     * @throws Exception When Measure is not found
     */
    public function find(int $id): Model
    {
        $model = Measure::where('id', $id)->first();
        if (!$model) {
            throw new Exception("Measure with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Measure
     *
     * @param MeasureDTO $data DTO containing Measure data
     * @return Measure Newly created Measure
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Measure::create([
            'name' => $data->name,
            'abbreviation' => $data->abbreviation,
        ]);
    }

    /**
     * Update an existing Measure
     *
     * @param MeasureDTO $data DTO containing updated Measure data
     * @return Measure Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'name' => $data->name,
            'abbreviation' => $data->abbreviation,
        ])) {
            throw new Exception("Failed to update Measure: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Measure
     *
     * @param int $id Measure ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
