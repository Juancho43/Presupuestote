<?php
// app/Repository/V1/ClientRepository.php
namespace App\Repository\V1;

use App\DTOs\V1\ClientDTO;
use App\Models\Client;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use \Exception;


class ClientRepository implements IRepository
{

    /**
     * Get all Clients
     *
     * @param int $page
     * @param int $perPage
     * @return Paginator Collection of Client models
     * @throws Exception If database query fails
     */

    public function all(int $page = 1): Paginator
    {
        return Client::with('person')->simplePaginate(getenv('PER_PAGE'), page:$page);
    }

    /**
     * Find a Client by ID
     *
     * @param int $id Client ID to find
     * @return Client Found Client model
     * @throws Exception When Client is not found
     */
    public function find(int $id): Model
    {
        $model = Client::with(['budgets', 'person'])->where('id', $id)->first();
        if (!$model) {
            throw new Exception("Client with id: {$id} not found");
        }
        return $model;
    }

    /**
     * Create a new Client
     *
     * @param ClientDTO $data DTO containing Client data
     * @return Client Newly created Client
     * @throws Exception If creation fails
     */
    public function create($data): Model
    {
        return Client::create([
            'balance' => $data->balance,
            'person_id' => $data->person->id,
        ]);
    }

    /**
     * Update an existing Client
     *
     * @param ClientDTO $data DTO containing updated Client data
     * @return Client Updated model
     * @throws Exception When update fails
     */
    public function update($data): Model
    {
        $model = $this->find($data->id);

        if (!$model->update([
            'balance' => $data->balance,
        ])) {
            throw new Exception("Failed to update Client: Database update failed");
        }

        return $model->fresh();
    }

    /**
     * Delete a Client
     *
     * @param int $id Client ID to delete
     * @return bool True if deleted successfully
     * @throws Exception If deletion fails
     */
    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
