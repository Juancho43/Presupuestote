<?php

namespace App\Services\V1;

use App\DTOs\V1\PaymentDTO;
use App\Events\NewPayment;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Http\Requests\V1\PaymentRequest;
use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Salary;
use App\Repository\V1\BudgetRepository;
use App\Repository\V1\PaymentRepository;
use App\States\PaymentState\Pago;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Type\Decimal;

class PaymentService
{
    use ApiResponseTrait;
    private PaymentRepository $repository;
    private BudgetRepository $budgetRepository;

    private static ?PaymentService $instance = null;

    public static function getInstance(): PaymentService
    {
        if (self::$instance === null) {
            self::$instance = new self(new PaymentRepository(), new BudgetRepository());
        }
        return self::$instance;
    }
    public function __construct(PaymentRepository $repository , BudgetRepository $budgetRepository)
    {
        $this->budgetRepository = $budgetRepository;
        $this->repository = $repository;
    }

    private function addPayment(PaymentDTO $data): Payment
    {
        $payment = $this->repository->create($data);
        return $payment;
    }
    private function deletePayment(int $id)
    {
        $this->repository->delete($id);
    }

 public function processModelPayment(string $modelClass, PaymentRequest $data, string $modelType): Payment | JsonResponse
 {
     try {
         $model = $modelClass::findOrFail($data->payable_id);
         $currentDebt = $model->calculateDebt();
         if ($data->amount > $currentDebt) {
             throw new \Exception("Payment amount exceeds the remaining {$modelType} debt by: " . ($data->amount - $currentDebt));
         }
         $payment = $this->addPayment(new PaymentDTO(null,
             new Decimal($data->amount),
             new Carbon($data->date),
             $data->description,
             $data->payable_type,
             $data->payable_id
         ));
         $model = $modelClass::findOrFail($data->payable_id);
         $newDebt = $model->calculateDebt();
         if ($newDebt == 0) {
             $this->updatePaymentStatus($model, Pago::class);
         }

         return $payment;
     } catch (\Exception $e) {
         return $this->errorResponse("Service error: adding payment to a {$modelType}", $e->getMessage(), \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
     }
 }
    private function updatePaymentStatus($model, $statusClass): void
    {
        $model->payment_status->transitionTo($statusClass);
        $model->save();
    }

}
