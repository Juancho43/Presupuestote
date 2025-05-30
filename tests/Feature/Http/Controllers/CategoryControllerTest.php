<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Traits\WithAuthentication;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }
    public function test_index_returns_categories_list(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ]
                ]
            ]);
    }

    public function test_show_returns_category_information()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'subcategories'
                ]
            ]);
    }

    public function test_store_creates_new_category()
    {
        $data = [
            'name' => $this->faker->word,
        ];

        $response = $this->postJson('/api/v1/categories', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => $data['name'],
        ]);
    }

    public function test_update_updates_existing_category()
    {
        $category = Category::factory()->create();
        $data = [
            'name' => $this->faker->word,
        ];

        $response = $this->putJson("/api/v1/categories/{$category->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $data['name'],
        ]);
    }

    public function test_delete_should_not_return_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/categories/{$category->id}");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => "Service Error: can't find Category"
            ]);
    }

}
