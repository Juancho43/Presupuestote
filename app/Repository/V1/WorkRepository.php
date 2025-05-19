<?php

namespace App\Repository\V1;

use App\DTOs\V1\WorkDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Work;
use Illuminate\Database\Eloquent\Collection;
use \Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkRepository
 *
 * Repository class for handling Work CRUD operations
 * Implements IRepository interface and uses ApiResponseTrait
 */
class WorkRepository implements IRepository
{
    use ApiResponseTrait;

    /**
     * Get all Works
     *
     * @return Collection Collection of Work models
     */
    public function all(): Collection
    {
        return Work::all();
    }

    /**
     * Find a Work by ID
     *
     * @param int $id Work ID to find
     * @return Work|JsonResponse Found Work model or error response
     * @throws Exception When Work is not found
     */
    public function find(int $id): Work|JsonResponse
    {
        $model = Work::with(['materials.prices','budget'])->where('id', $id)->firstOrFail();
        if (!$model) {
            throw new Exception('Error to find the resource with id: ' . $id);
        }
        return $model;
    }

    /**
     * Create a new Work
     *
     * @param WorkDTO $data Request containing Work data
     * @return Work Newly created Work model
     */
    public function create($data): Work
    {

        $model = Work::create([
            'order' => $data->order,
            'name' => $data->name,
            'notes' => $data->notes,
            'estimated_time' => $data->estimated_time,
            'dead_line' => $data->dead_line,
            'cost' => $data->cost ?? 0,
            'budget_id' => $data->budget->id
        ]);





        return $model;
    }

    /**
     * Update an existing Work
     *
     * @param int $id Work ID to update
     * @param WorkDTO $data Request containing updated Work data
     * @return Work|JsonResponse
     */
 public function update(int $id,$data): Work|JsonResponse
 {
     try {
         $work = $this->find($id);

         // Update Work fields
         $work->update([
             'order' => $data->order,
             'name' => $data->name,
             'notes' => $data->notes,
             'estimated_time' => $data->estimated_time,
             'dead_line' => $data->dead_line,
             'cost' => $data->cost ?? 0,
             'budget_id' => $data->budget->id
         ]);

         // Update materials with quantities if provided
         /*if ($data->has('materials')) {
             // materials: array of ['id' => material_id, 'quantity' => value]
             $materials = collect($data->input('materials'))
                 ->mapWithKeys(fn($item) => [$item['id'] => ['quantity' => $item['quantity']]])
                 ->toArray();
             $work->materials()->sync($materials);
         }*/
         return $work->fresh('materials');
     } catch (Exception $e) {
         return $this->errorResponse('Error to update the resource', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
     }
 }
 /**
     * Delete a Work
     *
     * @param int $id Work ID to delete
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
