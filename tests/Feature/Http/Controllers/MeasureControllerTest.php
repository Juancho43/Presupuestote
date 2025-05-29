<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Measure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MeasureControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_measure_list(): void
    {
        Measure::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/measures');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'abbreviation',
                    ]
                ]
            ]);
    }

    public function test_show_returns_measure_information()
    {
        $measure = Measure::factory()->create();

        $response = $this->getJson("/api/v1/measures/{$measure->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'abbreviation',
                ]
            ]);
    }

    public function test_store_creates_new_measure()
    {
        $data = [
            'name' => $this->faker->word(),
            'abbreviation' => "kg",
        ];

        $response = $this->postJson('/api/v1/measures', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'abbreviation',
                ]
            ]);

        $this->assertDatabaseHas('measures', [
            'name' => $data['name'],
            'abbreviation' => $data['abbreviation'],
        ]);

    }

    public function test_update_updates_existing_measure()
    {

        $measure = Measure::factory()->create();
        $data = [
            'name' => $this->faker->word(),
            'abbreviation' => 'kg',
        ];


        $response = $this->putJson("/api/v1/measures/{$measure->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'abbreviation',
                ]
            ]);

        $this->assertDatabaseHas('measures', [
            'id' => $measure->id,
            'name' => $data['name'],
            'abbreviation' => $data['abbreviation'],
        ]);
    }

    public function test_delete_should_not_return_measure()
    {
        $measure = Measure::factory()->create();

        $response = $this->deleteJson("/api/v1/measures/{$measure->id}");
        $response->assertStatus(204);

        $response = $this->getJson("/api/v1/measures/{$measure->id}");
        $response->assertStatus(404)
            ->assertJson([
                'message' => "Service Error: can't find Measure"
            ]);
    }
}
