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
            'password' => 'password',
            'role' => 'admin',
            'verified' => true,
        ]);

        $this->call([ClientNestedDataSeeder::class, SupplierNestedDataSeeder::class, EmployeeNestedDataSeeder::class, MaterialWithStockAndPriceSeeder::class, WorkWithMultipleMaterialsSeeder::class]);

    }



}
