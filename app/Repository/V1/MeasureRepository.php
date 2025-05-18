<?php

namespace App\Repository\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeasureRepository
 *
 * Repository class for handling Measure CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class MeasureRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Measures
     *
     * @return Collection Collection of Measure models
     */
    public function all(): Collection
    {
        return Measure::all();
    }

    /**
     * Find a Measure by ID
     *
     * @param int $id Measure ID to find
     * @return Measure|JsonResponse Found Measure model or error response
     * @throws Exception When Measure is not found
     */
    public function find(int $id): Measure|JsonResponse
    {
        $model = Measure::where('id', $id)->first();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Measure
     *
     * @param FormRequest $data Request containing Measure data
     * @return Measure Newly created Measure model
     */
    public function create(FormRequest $data): Measure
    {
        $data->validated();
        $model = Measure::create([
            'name' => $data->input('name'),
            'abbreviation' => $data->input('abbreviation'),
            'unit' => $data->input('unit'),

        ]);
        return $model;
    }

    /**
     * Update an existing Measure
     *
     * @param int $id Measure ID to update
     * @param FormRequest $data Request containing updated Measure data
     * @return Measure|JsonResponse
     */
    public function update(int $id, FormRequest $data): Measure|JsonResponse
    {
        try {
            $data->validated();
            $model = $this->find($id)->update(
                [
                    'name' => $data->input('name'),
                    'abbreviation' => $data->input('abbreviation'),
                    'unit' => $data->input('unit'),

                ]
            );
            $model->fresh();
            return $model;
        } catch (Exception $e) {
            return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Measure
     *
     * @param int $id Measure ID to delete
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
