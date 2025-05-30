<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class SubCategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_categories_list(): void
    {
        SubCategory::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/subcategories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category'
                    ]
                ]
            ]);
    }
    public function test_show_returns_subcategory_information()
    {
        $SubCategory = SubCategory::factory()->create();

        $response = $this->getJson("/api/v1/subcategories/{$SubCategory->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'category'
                ]
            ]);
    }
    public function test_store_creates_new_subcategory()
    {
        $category = Category::factory()->create();
        $data = [
            'name' => $this->faker->word,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/v1/subcategories', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ]);

        $this->assertDatabaseHas('sub_categories', [
            'name' => $data['name'],
            'category_id' => $data['category_id'],
        ]);
    }
    public function test_update_updates_existing_subcategory()
    {
        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);
        $data = [
            'name' => $this->faker->word,
            'category_id' => $category->id,
        ];

        $response = $this->putJson("/api/v1/subcategories/{$subcategory->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ]);

        $this->assertDatabaseHas('sub_categories', [
            'id' => $subcategory->id,
            'name' => $data['name'],
            'category_id' => $data['category_id'],
        ]);
    }
    public function test_delete_should_not_return_subcategory()
    {
        $subcategory = SubCategory::factory()->create();

        $response = $this->deleteJson("/api/v1/subcategories/{$subcategory->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/subcategories/{$subcategory->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => "Service Error: can't find SubCategory"
            ]);
    }

}
