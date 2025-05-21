<?php

namespace App\Repository\V1;

use App\DTOs\V1\PersonDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PersonRepository
 *
 * Repository class for handling Person CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class PersonRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Persons
     *
     * @return Collection Collection of Person models
     */
    public function all(): Collection
    {
        return Person::all();
    }

    /**
     * Find a Person by ID
     *
     * @param int $id Person ID to find
     * @return Person|JsonResponse Found Person model or error response
     * @throws Exception When Person is not found
     */
    public function find(int $id): Person|JsonResponse
    {
        $model = Person::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Person
     *
     * @param PersonDTO $data Request containing Person data
     * @return Person Newly created Person model
     */
    public function create($data): Person
    {
        $model = Person::create([
            [
                'name' => $data->name,
                'last_name' => $data->last_name,
                'address' => $data->address,
                'phone_number' => $data->phone_number,
                'mail' => $data->mail,
                'dni' => $data->dni,
                'cuit' => $data->cuit,
            ]
        ]);
        return $model;
    }

    /**
     * Update an existing Person
     *
     * @param int $id Person ID to update
     * @param PersonDTO $data Request containing updated Person data
     * @return Person|JsonResponse
     */
    public function update(int $id,$data): Person|JsonResponse
    {
        try {
            $model = $this->find($id)->update(
               [
                   'name' => $data->name,
                   'last_name' => $data->last_name,
                   'address' => $data->address,
                   'phone_number' => $data->phone_number,
                   'mail' => $data->mail ?? $this->find($id)->mail,
                   'dni' => $data->dni ?? $this->find($id)->dni,
                   'cuit' => $data->cuit ?? $this->find($id)->cuit,
               ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Person
     *
     * @param int $id Person ID to delete
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
