<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use App\Services\V1\ClientService;
use App\Services\V1\EmployeeService;
use App\Services\V1\SupplierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePeopleBalance
{

    protected ClientService $client_service;
    protected SupplierService $supplier_service;
    protected EmployeeService $employee_service;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->client_service = ClientService::getInstance();
        $this->supplier_service = SupplierService::getInstance();
        $this->employee_service = EmployeeService::getInstance();
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentCreated $event): void
    {
        if (isset($event->payable_entity->client_id)) {
            $this->client_service->updateBalance($event->payable_entity->client_id);
        }

        if (isset($event->payable_entity->supplier_id)) {
            $this->supplier_service->updateBalance($event->payable_entity->supplier_id);
        }

        if (isset($event->payable_entity->employee_id)) {
            $this->employee_service->updateBalance($event->payable_entity->employee_id);
        }

    }
}
