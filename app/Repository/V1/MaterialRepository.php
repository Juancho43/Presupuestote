<?php
namespace App\Repository\V1;

use App\Models\Material;
use App\DTOs\V1\MaterialDTO;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class MaterialRepository implements IRepository
{
    /**
     * Get all Materials
     *
     * @return Paginator Collection of Material models
     * @throws Exception If database query fails
     */
    public function all(int $page = 1):Paginator
    {
        return Material::with([
            'measure',
            'subcategory',
            'latestPrice',
            'latestStock',
      ])->simplePaginate(perPage: config('app.per_page'), page: $page);
    }

    /**
     * Find a Material by ID
     *
     * @param int $id Material ID to find
     * @return Material Found Material model
     * @throws Exception When Material is not found
     */
    public function find(int $id): Model
    {
        $model = Material::with([
            'measure' => function($query) {
                $query->select('id', 'name');
            },
            'subcategory' => function($query) {
                $query->select('id', 'name', 'category_id')
                    ->with(['category' => function($query) {
                        $query->select('id', 'name');
                    }]);
            }
        ])->find($id);
        if (!$model) {
            throw new Exception("Material with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Material
     *
     * @param MaterialDTO $data DTO containing Material data
     * @return Material Newly created Material
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Material::create([
            'name' => $data->name,
            'description' => $data->description,
            'color' => $data->color,
            'brand' => $data->brand,
            'unit_measure' => $data->unit_measure,
            'sub_category_id' => $data->subcategory->id,
            'measure_id' => $data->measure->id
        ]);
    }

    /**
     * Update an existing Material
     *
     * @param MaterialDTO $data DTO containing updated Material data
     * @return Material Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'name' => $data->name ?? $model->name,
            'description' => $data->description ?? $model->description,
            'color' => $data->color ?? $model->color,
            'brand' => $data->brand ?? $model->brand,
            'unit_measure' => $data->unit_measure ?? $model->unit_measure,
            'sub_category_id' => $data->subcategory->id ?? $model->subcategory_id,
            'measure_id' => $data->measure->id ?? $model->measure_id,
        ])) {
            throw new Exception("Failed to update Material: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Material
     *
     * @param int $id Material ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function getWithInvoices(int $id): Material
    {
        $model = Material::with(['invoices'])->findOrFail($id);
        if (!$model) {
            throw new Exception("Material with id: {$id} not found");
        }
        return $model;
    }

    public function getWithWorks(int $id): Material
    {
        $model = Material::with(['works'])->findOrFail($id);
        if (!$model) {
            throw new Exception("Material with id: {$id} not found");
        }
        return $model;
    }

    public function getWithStocks(int $id): Material
    {
        $model = Material::with(['stocks'])->findOrFail($id);
        if (!$model) {
            throw new Exception("Material with id: {$id} not found");
        }
        return $model;
    }

    public function getWithPrices(int $id): Material
    {
        $model = Material::with(['prices'])->findOrFail($id);
        if (!$model) {
            throw new Exception("Material with id: {$id} not found");
        }
        return $model;
    }

    public function getAll(): Collection
    {
        return Material::with([
            'measure',
            'subcategory',
            'latestPrice',
            'latestStock',
        ])->get();
    }

    public function search(string $query): Collection
    {
        return Material::with([
            'measure',
            'subcategory',
            'latestPrice',
            'latestStock',
        ])->where('name', 'like', "%{$query}%")
          ->orWhere('description', 'like', "%{$query}%")
          ->get();
    }
}
