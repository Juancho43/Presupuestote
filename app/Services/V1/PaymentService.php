<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\PaymentRepository;
use App\DTOs\V1\PaymentDTO;
use App\Models\Payment;
use App\States\PaymentState\Pago;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentService
 *
 * Service layer for handling business logic related to Payment entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class PaymentService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var PaymentService|null
     */
    private static ?PaymentService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var PaymentRepository
     */
    private PaymentRepository $repository;

    /**
     * Get or create the singleton instance
     *
     * @return PaymentService
     */
    public static function getInstance(): PaymentService
    {
        if (self::$instance === null) {
            self::$instance = new self(new PaymentRepository());
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param PaymentRepository $repository Repository for data operations
     */
    public function __construct(PaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Payment entity by ID
     *
     * @param int $id The entity ID
     * @return Payment|JsonResponse The found entity or error response
     */
    public function get(int $id): Model|JsonResponse
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't find Payment",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Payment entities
     *
     * @return Collection|JsonResponse Collection of entities or error response
     */
    public function getAll(): Collection|JsonResponse
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
     * Create a new Payment entity
     *
     * @param PaymentDTO $data Data transfer object containing entity information
     * @return Payment|JsonResponse The created entity or error response
     */
    public function create(PaymentDTO $data): Model|JsonResponse
    {
        try {
            $debt = $this->checkDebts($data->payable_type, $data->payable_id, $data->amount);
            if($debt['success'] == false){
                throw new Exception($debt['message']);
            }
            $payment = $this->repository->create($data);

            return $this->successResponse($payment,$debt['message'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Payment",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Payment entity
     *
     * @param PaymentDTO $data Data transfer object containing updated information
     * @return Payment|JsonResponse The updated entity or error response
     */
    public function update(PaymentDTO $data): Model|JsonResponse
    {
        try {
            $debt = $this->checkDebts($data->payable_type, $data->payable_id, $data->amount);
            if($debt['success'] == false){
                throw new Exception($debt['message']);
            }
            $updatedPayment = $this->repository->update($data);
            return $this->successResponse($updatedPayment,$debt['message'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't update Payment",
                $e->getMessage(),
            Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete a Payment entity by ID
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
                "Service Error: can't delete Payment",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    private function checkDebts($modelClass, $modelId, $amount)
    {
        $model = $modelClass::findOrFail($modelId);
        $currentDebt = $model->calculateDebt();
        $newDebt = $currentDebt - $amount;
        $response["message"] = "Payment amount is valid current debt: ". $newDebt;
        $response['success'] = true;
        $response['previous_debt'] = $currentDebt;
        if ($amount >$currentDebt) {
            $response['message'] = "Payment amount exceeds the remaining {$modelClass} debt: ". $newDebt;
            $response['success'] = false;
        }
        if ($newDebt == 0){
            $this->updatePaymentStatus($model, Pago::class);
            $response['message'] = "Payment completed successfully, {$modelClass} is fully paid.";
        }
        return $response;
    }

    private function updatePaymentStatus($model, $statusClass): void
    {
        $model->payment_status->transitionTo($statusClass);
        $model->save();
    }

    public function allClientPayments(int $id): Collection|JsonResponse
    {
        try {
            return $this->repository->allClientPayments($id);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve client payments",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function allEmployeePayments(int $id): Collection|JsonResponse
    {
        try {
            return $this->repository->allEmployeePayments($id);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve employee payments",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function allSupplierPayments(int $id): Collection|JsonResponse
    {
        try {
            return $this->repository->allSupplierPayments($id);
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't retrieve supplier payments",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
