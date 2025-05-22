<?php
namespace App\Services\V1;

use App\DTOs\V1\PriceDTO;
use App\DTOs\V1\StockDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\InvoiceRepository;
use App\DTOs\V1\InvoiceDTO;
use App\Models\Invoice;
use App\Repository\V1\PriceRepository;
use App\Repository\V1\StockRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvoiceService
 *
 * Service layer for handling business logic related to Invoice entity.
 * Implements the Singleton pattern for resource efficiency.
 *
 * @package App\Services\V1
 */
class InvoiceService
{
    use ApiResponseTrait;

    /**
     * Singleton instance
     *
     * @var InvoiceService|null
     */
    private static ?InvoiceService $instance = null;

    /**
     * Repository for data access operations
     *
     * @var InvoiceRepository
     */
    private InvoiceRepository $repository;
    private PriceRepository $priceRepository;
    private StockRepository $stockRepository;

    /**
     * Get or create the singleton instance
     *
     * @return InvoiceService
     */
    public static function getInstance(): InvoiceService
    {
        if (self::$instance === null) {
            self::$instance = new self(
                new InvoiceRepository(),
                new PriceRepository(),
                new StockRepository()
            );
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @param InvoiceRepository $repository Repository for data operations
     */
    public function __construct(
        InvoiceRepository $repository,
        PriceRepository $priceRepository,
        StockRepository $stockRepository
    )
    {
        $this->priceRepository = $priceRepository;
        $this->stockRepository = $stockRepository;
        $this->repository = $repository;
    }

    /**
     * Retrieve a specific Invoice entity by ID
     *
     * @param int $id The entity ID
     * @return Invoice|JsonResponse The found entity or error response
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
                "Service Error: can't find Invoice",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Retrieve all Invoice entities
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
     * Create a new Invoice entity
     *
     * @param InvoiceDTO $data Data transfer object containing entity information
     * @return Invoice|JsonResponse The created entity or error response
     */
    public function create(InvoiceDTO $data): Model|JsonResponse
    {
        try {
            $newInvoice = $this->repository->create($data);
            return $newInvoice;
        } catch (Exception $e) {
            return $this->errorResponse(
                "Service Error: can't create Invoice",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update an existing Invoice entity
     *
     * @param InvoiceDTO $data Data transfer object containing updated information
     * @return Invoice|JsonResponse The updated entity or error response
     */
    public function update(InvoiceDTO $data): Model|JsonResponse
    {
        try {
            $updatedInvoice = $this->repository->update($data);
            return $updatedInvoice;
        } catch (Exception $e) {
            $statusCode = str_contains($e->getMessage(), "not found")
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                "Service Error: can't update Invoice",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * Delete a Invoice entity by ID
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
                "Service Error: can't delete Invoice",
                $e->getMessage(),
                $statusCode
            );
        }
    }

    private function generateInvoiceMaterialPivot(array $materials) : array | JsonResponse
    {
        $pivotData = [];
        foreach ($materials as $materialData)
        {
            $price = new Decimal($materialData['price']);
            $stock = new Decimal($materialData['quantity']);
            $date = new Carbon();
            $newPrice = $this->priceRepository->create(new PriceDTO(price: $price, date: $date, material: new MaterialDTO(id:$materialData['id'])));
            $newStock = $this->stockRepository->create(new StockDTO(stock: $stock, date: $date, material: new MaterialDTO(id: $materialData['id'])));
            $pivotData[$materialData['id']] = [
                'quantity' => $materialData['quantity'],
                'price_id' => $newPrice->id,
                'stock_id' => $newStock->id
            ];
        }

        return $pivotData;
    }

    public function addMaterialsToInvoice(FormRequest $data) : Invoice | JsonResponse
    {
        try {
            $invoice = $this->repository->find($data->invoice_id);
            $syncData = $this->generateInvoiceMaterialPivot($data->materials);
            $invoice->materials()->sync($syncData);
            $invoice->save();
            $invoice->updateTotal();
            return $invoice;
        }catch (Exception $e) {
            return $this->errorResponse('Error adding works to budget', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
