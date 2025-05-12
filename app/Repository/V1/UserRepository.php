<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserRepository
 *
 * Repository class for handling User CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class UserRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Users
     *
     * @return Collection Collection of User models
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Find a User by ID
     *
     * @param int $id User ID to find
     * @return User|JsonResponse Found User model or error response
     * @throws Exception When User is not found
     */
    public function find(int $id): User|JsonResponse
    {
        $model = User::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new User
     *
     * @param FormRequest $data Request containing User data
     * @return User Newly created User model
     */
    public function create(FormRequest $data): User
    {
        $data->validated();
        $model = User::create($data->all());
        return $model;
    }

    /**
     * Update an existing User
     *
     * @param int $id User ID to update
     * @param FormRequest $data Request containing updated User data
     * @return User|JsonResponse
     */
    public function update(int $id, FormRequest $data): User|JsonResponse
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
     * Delete a User
     *
     * @param int $id User ID to delete
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
