<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ClientRepository
 *
 * Repository class for handling Client CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class ClientRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Clients
     *
     * @return Collection Collection of Client models
     */
    public function all(): Collection
    {
        return Client::all();
    }

    /**
     * Find a Client by ID
     *
     * @param int $id Client ID to find
     * @return Client|JsonResponse Found Client model or error response
     * @throws Exception When Client is not found
     */
    public function find(int $id): Client|JsonResponse
    {
        $model = Client::with(['budgets', 'person'])->where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Client
     *
     * @param FormRequest $data Request containing Client data
     * @return Client Newly created Client model
     */
    public function create(FormRequest $data): Client
    {
        $data->validated();
        $model = Client::create($data->all());
        return $model;
    }

    /**
     * Update an existing Client
     *
     * @param int $id Client ID to update
     * @param FormRequest $data Request containing updated Client data
     * @return Client|JsonResponse
     */
    public function update(int $id, FormRequest $data): Client|JsonResponse
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
     * Delete a Client
     *
     * @param int $id Client ID to delete
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
