<?php
namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Payment;
use App\Repository\V1\PaymentRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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
    public function get(int $id): Payment | JsonResponse
    {
        try {
            return $this->repository->find($id);
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't find dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve all Payment entities
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
     * Create a new Payment entity
     *
     * @param PaymentDTO $data Data transfer object containing entity information
     * @return Payment|JsonResponse The created entity or error response
     */
    public function create(PaymentDTO $data): Payment | JsonResponse
    {
        try {
            $newPayment = $this->repository->create($data);
            $newPayment->fresh();
            return $newPayment;
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't create dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing Payment entity
     *
     * @param PaymentDTO $data Data transfer object containing updated information
     * @return Payment|JsonResponse The updated entity or error response
     */
    public function update(PaymentDTO $data): Payment | JsonResponse
    {
        try {
            $newPayment = $this->repository->update($data->id, $data);
            $newPayment->fresh();
            return $newPayment;
        } catch (Exception $e) {
            return $this->errorResponse("Service Error: can't update dummy", $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Payment entity by ID
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

    public function processModelPayment(string $modelClass, PaymentRequest $data, string $modelType): array | JsonResponse
    {
        try {
            $model = $modelClass::findOrFail($data->payable_id);
            $currentDebt = $model->calculateDebt();
            if ($data->amount > $currentDebt) {
                throw new \Exception("Payment amount exceeds the remaining {$modelType} debt by: " . ($data->amount - $currentDebt));
            }
            $payment = $this->create(new PaymentDTO(null,
                new Decimal($data->amount),
                new Carbon($data->date),
                $data->description,
                $data->payable_type,
                $data->payable_id
            ));
            $model = $modelClass::findOrFail($data->payable_id);
            $newDebt = $model->calculateDebt();
            $response['message'] = "Current debt: " . $newDebt;
            if ($newDebt == 0) {
                $response['message'] = "Payment completed successfully, {$modelType} is fully paid.";
                $this->updatePaymentStatus($model, Pago::class);
            }
            $response['payment'] = $payment;
            return $response ;
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
