<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Salary;
use App\States\PaymentState\Pago;
use Illuminate\Database\Seeder;

class EmployeeNestedDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a person for the employee
        $person = Person::factory()->create([
        ]);

        // Create an employee
        $employee = Employee::factory()->create([
            'person_id' => $person->id,
            'salary' => 3000.00,
            'start_date' => now()->subYears(2),
            'is_active' => true,
        ]);

        // Create salaries for different months
        $salary1 = Salary::factory()->create([
            'employee_id' => $employee->id,
            'amount' => 3000.00,
            'date' => now()->subMonth(),
            'active' => true,
        ]);

        $salary2 = Salary::factory()->create([
            'employee_id' => $employee->id,
            'amount' => 3000.00,
            'date' => now(),
            'active' => true,
        ]);

        // Create payments for each salary
        Payment::factory()->create([
            'payable_type' => Salary::class,
            'payable_id' => $salary1->id,
            'amount' => 3000.00,
            'date' => now()->subMonth(),
            'description' => 'Monthly salary payment',
        ]);

        // Split current month's salary into two payments
        Payment::factory()->create([
            'payable_type' => Salary::class,
            'payable_id' => $salary2->id,
            'amount' => 1500.00,
            'date' => now(),
            'description' => 'First half of monthly salary',
        ]);

        Payment::factory()->create([
            'payable_type' => Salary::class,
            'payable_id' => $salary2->id,
            'amount' => 1500.00,
            'date' => now()->addDays(15),
            'description' => 'Second half of monthly salary',
        ]);

        $salary1->payment_status->transitionTo(Pago::class);
        $salary1->save();
        $salary2->payment_status->transitionTo(Pago::class);
        $salary2->save();

    }
}
