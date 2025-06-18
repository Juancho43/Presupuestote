<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Price;
use App\Models\Stock;
use App\Repository\V1\WorkRepository;
use App\DTOs\V1\WorkDTO;
use App\Models\Work;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkService
 *
 * Service layer for handling business logic related to Work entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class WorkService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var WorkService|null
     */
    private static ?WorkService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var WorkRepository
     */
    private WorkRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return WorkService
     */
    public static function getInstance(): WorkService
    {
        if (self::$instance === null) {
            self::$instance = new self(new WorkRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param WorkRepository $repository Repository for data operations
     */
    public function __construct(WorkRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Work entity by ID
     *
     * @param int $id The entity ID
     * @return Work|JsonResponse The found entity or error response
     */
    public function get(int $id): Work|JsonResponse
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't find Work",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Work entities
     *
     * @return Paginator|JsonResponse Collection of entities or error response
     */
    public function getAll(): Paginator|JsonResponse
    {
        try {
            return $this->repository->all();
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve dummies",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create a new Work entity
     *
     * @param WorkDTO $data Data transfer object containing entity information
     * @return Work|JsonResponse The created entity or error response
     */
    public function create(WorkDTO $data): Work|JsonResponse
    {
        try {
            $work = $this->repository->create($data);
            return $work;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Work",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Work entity
     *
     * @param WorkDTO $data Data transfer object containing updated information
     * @return Work|JsonResponse The updated entity or error response
     */
    public function update(WorkDTO $data): Model|JsonResponse
    {
        try {
            return $this->repository->update($data);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Work",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Work entity by ID
     *
     * @param int $id The entity ID
     * @return bool|JsonResponse True if successful or error response
     */
    public function delete(int $id): bool|JsonResponse
    {
        try {
            return $this->repository->delete($id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't delete Work",
                $e->getMessage(),
                $statusCode
            );
        }
    }
    public function addMaterialsToWork(FormRequest $request): Work | JsonResponse
    {
        try {
            $work = $this->repository->find($request->input('work_id'));
            $syncData = $this->generateMaterialWorksPivot($request->input('materials'));
            $work->materials()->sync($syncData);
            $work->refresh();
            $work->updateCost();
            $work->budget->updatePrice();

            return $work;
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: adding materials to work failed", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateMaterialWorksPivot(array $materials) : array | JsonResponse
    {
        $pivotData = [];

        foreach ($materials as $materialData) {
            // Get the latest price and stock for the material
            $latestPrice = Price::where('material_id', $materialData['id'])
                ->latest('date')
                ->first();

            $latestStock = Stock::where('material_id', $materialData['id'])
                ->latest('date')
                ->first();

            if (!$latestPrice || !$latestStock) {
                return $this->errorResponse(
                    "Service Error: Material missing price or stock",
                    "Material ID {$materialData['id']} has no price or stock records",
                );
            }

            $pivotData[$materialData['id']] = [
                'quantity' => $materialData['quantity'],
                'price_id' => $latestPrice->id,
                'stock_id' => $latestStock->id
            ];
        }

        return $pivotData;
    }

    public function changeState(int $id, string $stateName): Work | JsonResponse
    {
        try {
            $work = $this->repository->find($id);
            if($work->state->canTransitionTo($stateName)) {
                $work->state->transitionTo($stateName);
                $work->save();
            }else{
                throw new Exception("State transition failed");
            }
            return $work;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't change state",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function search(string $query): Collection | JsonResponse
    {
        try {
            return $this->repository->search($query);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't search works",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
