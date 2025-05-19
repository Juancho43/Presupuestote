<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Http\Requests\V1\BudgetRequest;
use App\Models\Budget;
use App\Repository\V1\BudgetRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class BudgetService
{
    use ApiResponseTrait;
    private BudgetRepository $repository;
    private WorkService $workService;

    private static ?BudgetService $instance = null;

    public static function getInstance(): BudgetService
    {
        if (self::$instance === null) {
            self::$instance = new self(new BudgetRepository());
        }
        return self::$instance;
    }
    public function __construct(BudgetRepository $repository)
    {

        $this->repository = $repository;
    }

    private function calculateBudgetCost(Budget $budget) : float
    {
       float : $cost = 0;
       foreach ($budget->works as $work){
           $cost += $work->cost;
       }
       return $cost;
    }

    private function calculateBudgetPrice(Budget $budget) : float
    {
        return $budget->cost + $budget->profit;
    }


    public function updateBudgetCost(Budget $budget) : Budget
    {
        $budget->cost = $this->calculateBudgetCost($budget);
        $budget->save();
        return $budget;
    }

    public function updateBudgetPrice(int $budgetId) : Budget
    {
        $budget = $this->repository->find($budgetId);
        $budget = $this->updateBudgetCost($budget);
        $budget->price = $this->calculateBudgetPrice($budget);

        $budget->save();
        echo $budget->price;
        return $budget;
    }


    public function updateBudget(int $id,BudgetRequest $data) : Budget | JsonResponse
    {
        $newBudget = $this->repository->update($id,$data);
        $newBudget = $this->updateBudgetPrice($newBudget->id);
        $newBudget->fresh();
        return $newBudget;
    }

    public function createBudget(BudgetRequest $data) : Budget | JsonResponse
    {
        $newBudget = $this->repository->create($data);
        $newBudget = $this->updateBudgetPrice($newBudget->id);
        $newBudget->fresh();
        return $newBudget;
    }

    /**
     * @throws Exception
     */
    public function addWorksToBudget(FormRequest $data): Budget | JsonResponse
    {
        try {
            $budget = $this->repository->addWorks($data->budget_id, $data->work_ids);
            return $budget;
        }catch (Exception $e) {
            return $this->errorResponse('Error adding works to budget', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}
