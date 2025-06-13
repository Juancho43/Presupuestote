<?php
namespace App\Repository\V1;

use App\Models\User;
use App\DTOs\V1\UserDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class UserRepository
{
    /**
     * Get all Users
     *
     * @return Collection Collection of User models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Find a User by ID
     *
     * @param int $id User ID to find
     * @return User Found User model
     * @throws Exception When User is not found
     */
    public function find(int $id): Model
    {
        $model = User::where('id', $id)->first();
        if (!$model) {
            throw new Exception("User with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new User
     *
     * @param UserDTO $data DTO containing User data
     * @return User Newly created User
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return User::create($data->all());
    }

    /**
     * Update an existing User
     *
     * @param UserDTO $data DTO containing updated User data
     * @return User Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update($data->all())) {
            throw new Exception("Failed to update User: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a User
     *
     * @param int $id User ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
