<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    // Seed the database using all available factories



        \App\Models\Payment::factory(10)->create();
        \App\Models\Salary::factory(10)->create();
        \App\Models\Invoice::factory(10)->create();
        \App\Models\Budget::factory(10)->create();

        \App\Models\Work::factory(10)->create();

        \App\Models\Client::factory(10)->create();
        \App\Models\Employee::factory(10)->create();
        \App\Models\Supplier::factory(10)->create();

        \App\Models\Category::factory(10)->create();
        \App\Models\SubCategory::factory(10)->create();
        \App\Models\Price::factory(10)->create();
        \App\Models\Stock::factory(10)->create();
        \App\Models\Measure::factory(10)->create();

        \App\Models\Material::factory(10)->create();
$this->call([ClientNestedDataSeeder::class]);
    }



}
