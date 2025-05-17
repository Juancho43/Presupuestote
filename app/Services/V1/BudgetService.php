<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Http\Requests\V1\AddWorksToBudgeRequest;
use App\Http\Requests\V1\BudgetRequest;
use App\Models\Budget;
use App\Repository\V1\BudgetRepository;
use App\Repository\V1\WorkRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Js;
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
           echo $cost;
       }
       return $cost;
    }


    public function updateBudgetCost(Budget $budget) : Budget
    {
        $budget->cost = $this->calculateBudgetCost($budget);
        $budget->save();
        return $budget;
    }

    public function updateBudget(BudgetRequest $data) : Budget | JsonResponse
    {
        $newBudget = $this->repository->update($data->id,$data);
        $newBudget = $this->updateBudgetCost($newBudget);
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
