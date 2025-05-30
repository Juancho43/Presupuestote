<?php
// app/Repository/V1/CategoryRepository.php
namespace App\Repository\V1;

use App\DTOs\V1\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class CategoryRepository implements IRepository
{
    /**
     * Get all Categorys
     *
     * @return Collection Collection of Category models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find a Category by ID
     *
     * @param int $id Category ID to find
     * @return Model Found Category model
     * @throws Exception When Category is not found
     */
    public function find(int $id): Model
    {
        $model = Category::with('subcategories')->find($id);
        if (!$model) {
            throw new Exception("Category with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Category
     *
     * @param CategoryDTO $data DTO containing Category data
     * @return Model Newly created Category
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Category::create([
            'name' => $data->name,
        ]);
    }

    /**
     * Update an existing Category
     *
     * @param CategoryDTO $data DTO containing updated Category data
     * @return Model Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);

        if (!$model->update([  'name' => $data->name,])) {
            throw new Exception("Failed to update Category: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Category
     *
     * @param int $id Category ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
