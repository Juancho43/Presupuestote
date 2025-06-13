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
        //for testing purposes, create an admin user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'admin',
            'verified' => true,
        ]);

        for ($i = 0; $i < 2; $i++) {


            $this->call([
                ClientNestedDataSeeder::class,
                SupplierNestedDataSeeder::class,
                EmployeeNestedDataSeeder::class,
                MaterialWithStockAndPriceSeeder::class,
                WorkWithMultipleMaterialsSeeder::class,
                ExtendedMaterialSeeder::class
            ]);
        }

    }



}
