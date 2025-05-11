<?php

 namespace Tests\Unit\Http\Requests;

 use Tests\TestCase;
 use App\Models\Person;
 use App\Http\Requests\EmployeeRequest;
 use Illuminate\Support\Facades\Validator;
 use Illuminate\Foundation\Testing\RefreshDatabase;

 class EmployeeRequestTest extends TestCase
 {
     use RefreshDatabase;

     private function getRequest(array $data): array
     {
         $request = new EmployeeRequest();
         return $request->rules();
     }

     public function test_valid_employee_with_person_id_passes_validation()
     {
         $person = Person::factory()->create();

         $validator = Validator::make([
             'person_id' => $person->id,
             'salary' => '1000.00',
             'start_date' => '2024-03-20',
             'is_active' => true
         ], $this->getRequest([]));

         $this->assertFalse($validator->fails());
     }

     public function test_valid_employee_with_new_person_passes_validation()
     {
         $validator = Validator::make([
             'salary' => '1000.00',
             'start_date' => '2024-03-20',
             'is_active' => true,
             'person' => [
                 'name' => 'John Doe',
                 'phone_number' => '1234567890'
             ]
         ], $this->getRequest([]));

         $this->assertFalse($validator->fails());
     }

     public function test_end_date_must_be_after_start_date()
     {
         $validator = Validator::make([
             'salary' => '1000.00',
             'start_date' => '2024-03-20',
             'end_date' => '2024-03-19',
             'is_active' => true,
             'person' => [
                 'name' => 'John Doe',
                 'phone_number' => '1234567890'
             ]
         ], $this->getRequest([]));

         $this->assertTrue($validator->fails());
         $this->assertArrayHasKey('end_date', $validator->errors()->toArray());
     }
 }
