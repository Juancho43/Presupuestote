<?php
namespace App\Repository\V1;

use App\Models\SubCategory;
use App\DTOs\V1\SubCategoryDTO;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class SubCategoryRepository implements IRepository
{
    /**
     *
     * Get all SubCategorys
     *
     * @return Paginator Collection of SubCategory models
     * @throws Exception If database query fails
     */
    public function all(int $page = 1):Paginator
    {
        return SubCategory::with('category')->simplePaginate(getenv('PER_PAGE'), $page);
    }

    /**
     * Find a SubCategory by ID
     *
     * @param int $id SubCategory ID to find
     * @return SubCategory Found SubCategory model
     * @throws Exception When SubCategory is not found
     */
    public function find(int $id): Model
    {
        $model = SubCategory::with('category')->find($id);
        if (!$model) {
            throw new Exception("SubCategory with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new SubCategory
     *
     * @param SubCategoryDTO $data DTO containing SubCategory data
     * @return SubCategory Newly created SubCategory
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return SubCategory::create([
            'name' => $data->name,
            'category_id' => $data->category->id
        ]);
    }

    /**
     * Update an existing SubCategory
     *
     * @param SubCategoryDTO $data DTO containing updated SubCategory data
     * @return SubCategory Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'name' => $data->name ?? $model->name,
            'category_id' => $data->category->id ?? $model->category_id
        ])) {
            throw new Exception("Failed to update SubCategory: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a SubCategory
     *
     * @param int $id SubCategory ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function getAll(): Collection
    {
        return SubCategory::with('category')->get();
    }
}
