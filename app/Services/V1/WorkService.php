<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Price;
use App\Models\Work;
use App\Repository\V1\WorkRepository;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WorkService{
use ApiResponseTrait;
    private WorkRepository $repository;
    private static ?WorkService $instance = null;
    private BudgetService $budgetService;



    public static function getInstance(): WorkService
    {
        if (self::$instance === null) {
            self::$instance = new self(new WorkRepository());
        }
        return self::$instance;
    }



    public function __construct(WorkRepository $repository)
    {
        $this->budgetService = BudgetService::getInstance();
        $this->repository = $repository;
    }

    public function calculateWorkCost(int $workId): float
    {
        $work = $this->repository->find($workId);
        $cost = 0;

        if ($work->materials->isEmpty()) {
            return $cost;
        }

        foreach ($work->materials as $material) {
            $price = Price::find($material->pivot->price_id);
            $cost += $material->pivot->quantity * $price->price;

        }

        return $cost;
    }
    public function addMaterialsToWork(FormRequest $request): Work | JsonResponse
    {
        try {
            $request->validated();
            $work = $this->repository->find($request->work_id);

            $syncData = $this->generateMaterialWorksPivot($request->materials, $work);
            $work->materials()->sync($syncData);

            // Calculate and update the work cost
            $work->cost = $this->calculateWorkCost($work->id);

            $work->save();
            $this->budgetService->updateBudgetPrice($work->budget_id);
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
