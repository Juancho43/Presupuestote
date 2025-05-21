<?php

namespace App\Services\V1;

use App\DTOs\V1\MaterialDTO;
use App\DTOs\V1\PriceDTO;
use App\DTOs\V1\StockDTO;
use App\Http\Controllers\V1\ApiResponseTrait;
use App\Models\Invoice;
use App\Repository\V1\InvoiceRepository;
use App\Repository\V1\PriceRepository;
use App\Repository\V1\StockRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Type\Decimal;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{
    use ApiResponseTrait;
    private InvoiceRepository $repository;

    private PriceRepository $priceRepository;
    private StockRepository $stockRepository;
    private static ?InvoiceService $instance = null;

    public static function getInstance(): InvoiceService
    {
        if (self::$instance === null) {
            self::$instance = new self(new InvoiceRepository());
        }
        return self::$instance;
    }
    public function __construct(InvoiceRepository $repository, PriceRepository $priceRepository, StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->priceRepository = $priceRepository;
        $this->repository = $repository;
    }
    private function calculateInvoiceTotal(int $invoiceId) : float
    {
        $invoice = $this->repository->find($invoiceId);
        $total = 0;
        foreach ($invoice->materials as $material) {
            $total += $material->pivot_price * $material->pivot->quantity;
        }
        return $total;
    }
    public function updateInvoiceTotal(int $invoiceId) : Invoice
    {
        $invoice = $this->repository->find($invoiceId);
        $total = $this->calculateInvoiceTotal($invoiceId);
        $invoice->total = $total;
        $invoice->save();
        return $invoice;
    }
    public function addMaterialsToInvoice(FormRequest $data) : Invoice | JsonResponse
    {
        try {
            $data->validated();
            $invoice = $this->repository->find($data->invoice_id);
            $syncData = $this->generateInvoiceMaterialPivot($data->materials);
            $invoice->materials()->sync($syncData);
            $invoice->save();
            $this->updateInvoiceTotal($invoice->id);
            return $invoice;
        }catch (Exception $e) {
            return $this->errorResponse('Error adding works to budget', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function generateInvoiceMaterialPivot(array $materials) : array | JsonResponse
    {
        $pivotData = [];
        foreach ($materials as $materialData)
        {
            $price = new Decimal($materialData['price']);
            $stock = new Decimal($materialData['quantity']);
            $date = Date::now();
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
}
