<?php

namespace App\Services\V1;

use App\Http\Controllers\V1\ApiResponseTrait;
use App\Repository\V1\BudgetRepository;
use App\Repository\V1\InvoiceRepository;
use App\Repository\V1\PersonRepository;
use App\Repository\V1\SalaryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;
use http\Exception;

class OwnableService
{
    use ApiResponseTrait;
    /**
     * Singleton instance
     *
     * @var OwnableService|null
     */
    private static ?OwnableService $instance = null;
    private BudgetRepository $budgetRepository;
    private InvoiceRepository $invoiceRepository;
    private SalaryRepository $salaryRepository;

    /**
     * @param BudgetRepository $budgetRepository
     * @param InvoiceRepository $invoiceRepository
     * @param SalaryRepository $salaryRepository
     */
    public function __construct(BudgetRepository $budgetRepository, InvoiceRepository $invoiceRepository, SalaryRepository $salaryRepository)
    {
        $this->budgetRepository = $budgetRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->salaryRepository = $salaryRepository;
    }

    /**
     * Get or create the singleton instance
     *
     * @return PersonRepository
     */
    public static function getInstance(): OwnableService
    {
        if (self::$instance === null) {
            self::$instance = new self(new BudgetRepository(), new InvoiceRepository(), new SalaryRepository());
        }
        return self::$instance;
    }

    public function search(string $entity, string $search): Paginator|Collection|JsonResponse
    {
        $repositories = [
            'budget' => $this->budgetRepository,
            'invoice' => $this->invoiceRepository,
            'salary' => $this->salaryRepository,
        ];

        if (!isset($repositories[$entity])) {
            return $this->errorResponse('Entity not found', Response::HTTP_NOT_FOUND);
        }
        $results = $repositories[$entity]->all();
        if(strlen($search) > 3){
            $results = $repositories[$entity]->search($search);
        }


        return $results;
    }
}
