<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Http\Requests\V1\PersonRequest;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SupplierRepository
 *
 * Repository class for handling Supplier CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class SupplierRepository implements IRepository
{
    use ApiResponseTrait;
    private PersonRepository $personRepository;

    /**
     * @param PersonRepository $personRepository
     */
    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }
    /**
     * Get all Suppliers
     *
     * @return Collection Collection of Supplier models
     */
    public function all(): Collection
    {
        return Supplier::with('person')->get();
    }

    /**
     * Find a Supplier by ID
     *
     * @param int $id Supplier ID to find
     * @return Supplier|JsonResponse Found Supplier model or error response
     * @throws Exception When Supplier is not found
     */
    public function find(int $id): Supplier|JsonResponse
    {
        $model = Supplier::with(['person','invoice'])->findOrFail($id);
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Supplier
     *
     * @param FormRequest $data Request containing Supplier data
     * @return Supplier Newly created Supplier model
     */
    public function create(FormRequest $data): Supplier
    {
        $data->validated();

        // Create client with provided balance or default to 0
        $Employee = new Supplier([
            'salary',
            'start_date',
            'end_date',
            'is_active',
        ]);

        // Handle person relationship
        if ($data->has('person')) {
            $personRequest = new PersonRequest($data->person);
            $person = $this->personRepository->create($personRequest);
            $Employee->person()->associate($person);
        } else {
            // Associate with existing person
            $person = $this->personRepository->find($data->person_id);
            $Employee->person()->associate($person);
        }

        $Employee->save();
        return $Employee->load('person');
    }

    /**
     * Update an existing Supplier
     *
     * @param int $id Supplier ID to update
     * @param FormRequest $data Request containing updated Supplier data
     * @return Supplier|JsonResponse
     */
    public function update(int $id, FormRequest $data): Supplier|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                [
                    'notes' => $data->input('notes'),
                    'balance' => $data->input('balance'),
                ]
            );
            return $model->fresh()->load('person');
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Supplier
     *
     * @param int $id Supplier ID to delete
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
