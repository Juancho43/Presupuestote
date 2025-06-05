<?php
// app/Repository/V1/PersonRepository.php
namespace App\Repository\V1;

use App\DTOs\V1\PersonDTO;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class PersonRepository implements IRepository
{
    /**
     * Get all Persons
     *
     * @return Collection Collection of Person models
     * @throws Exception If database query fails
     */
    public function all(): Collection
    {
        return Person::all();
    }

    /**
     * Find a Person by ID
     *
     * @param int $id Person ID to find
     * @return Model Found Person model
     * @throws Exception When Person is not found
     */
    public function find(int $id): Model
    {
        $model = Person::where('id', $id)->first();
        if (!$model) {
            throw new Exception("Person with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Person
     *
     * @param PersonDTO $data DTO containing Person data
     * @return Model Newly created Person
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Person::create([
            'name' => $data->name,
            'last_name' => $data->last_name,
            'address' => $data->address,
            'phone_number' => $data->phone_number,
            'mail' => $data->mail,
            'dni' => $data->dni,
            'cuit' => $data->cuit,
        ]);
    }

    /**
     * Update an existing Person
     *
     * @param int $id Person ID to update
     * @param PersonDTO $data DTO containing updated Person data
     * @return Model Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);
        if (!$model->update([
            'name' => $data->name ?? $model->name,
            'last_name' => $data->last_name ?? $model->last_name,
            'address' => $data->address ?? $model->address,
            'phone_number' => $data->phone_number ?? $model->phone_number,
            'mail' => $data->mail ?? $model->mail,
            'dni' => $data->dni ?? $model->dni,
            'cuit' => $data->cuit ?? $model->cuit,
        ])) {
            throw new Exception("Failed to update Person: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Person
     *
     * @param int $id Person ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function search(string $query): Collection
    {
        return Person::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orWhere('phone_number', 'LIKE', "%{$query}%")
                ;
        })
        ->with(['employee', 'supplier', 'client'])
        ->get();
    }
}
