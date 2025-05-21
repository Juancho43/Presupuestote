<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Work;
use App\Repository\V1\WorkRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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

    private BudgetService $budgetService;

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
        $this->budgetService = BudgetService::getInstance();
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Work entity by ID
     *
     * @param int $id The entity ID
     * @return Work|JsonResponse The found entity or error response
     */
    public function get(int $id): Work | JsonResponse
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't find dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve all Work entities
     *
     * @return Collection|JsonResponse Collection of entities or error response
     */
    public function getAll(): Collection | JsonResponse
    {
        try {
            return $this->repository->all();
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't retrieve dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new Work entity
     *
     * @param WorkDTO $data Data transfer object containing entity information
     * @return Work|JsonResponse The created entity or error response
     */
    public function create(WorkDTO $data): Work | JsonResponse
    {
        try {
            $newWork = $this->repository->create($data);
            $newWork->fresh();
            return $newWork;
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't create dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing Work entity
     *
     * @param WorkDTO $data Data transfer object containing updated information
     * @return Work|JsonResponse The updated entity or error response
     */
    public function update(WorkDTO $data): Work | JsonResponse
    {
        try {
            $newWork = $this->repository->update($data->id, $data);
            $newWork->fresh();
            return $newWork;
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't update dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Work entity by ID
     *
     * @param int $id The entity ID
     * @return bool|JsonResponse True if successful or error response
     */
    public function delete(int $id): bool | JsonResponse
    {
        try {
            return $this->repository->delete($id);
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't delete dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addMaterialsToWork(FormRequest $request): Work | JsonResponse
    {
        try {
            $request->validated();
            $work = $this->repository->find($request->work_id);

            $syncData = $this->generateMaterialWorksPivot($request->materials, $work);
            $work->materials()->sync($syncData);

            $work->updateCost();

            $work->save();
            $work->budget->updatePrice();

            return $this->repository->find($work->id);
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: adding materials to work failed", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateMaterialWorksPivot(array $materials, Work $work) : array | JsonResponse
    {
        $pivotData = [];

        foreach ($materials as $materialData) {
            // Get the latest price and stock for the material
            $latestPrice = \App\Models\Price::where('material_id', $materialData['id'])
                ->latest('date')
                ->first();

            $latestStock = \App\Models\Stock::where('material_id', $materialData['id'])
                ->latest('date')
                ->first();

            if (!$latestPrice || !$latestStock) {
                return $this->errorResponse(
                    "Service Error: Material missing price or stock",
                    "Material ID {$materialData['id']} has no price or stock records",
                    Response::HTTP_BAD_REQUEST
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

}
