<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\V1\AddMaterialsToWorksRequest;
use App\Models\Material;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddMaterialsToWorksRequestTest extends TestCase
{
    use RefreshDatabase;

    private AddMaterialsToWorksRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new AddMaterialsToWorksRequest();
    }

    /** @test */
    public function it_validates_valid_request_data(): void
    {
        // Arrange
        $work = Work::factory()->create();
        $materials = Material::factory()->count(2)->create();

        $data = [
            'work_id' => $work->id,
            'materials' => [
                [
                    'id' => $materials[0]->id,
                    'quantity' => 2
                ],
                [
                    'id' => $materials[1]->id,
                    'quantity' => 3
                ]
            ]
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_requires_work_id(): void
    {
        // Arrange
        $materials = Material::factory()->count(2)->create();

        $data = [
            'materials' => [
                [
                    'id' => $materials[0]->id,
                    'quantity' => 2
                ]
            ]
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('work_id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_requires_valid_work_id(): void
    {
        // Arrange
        $data = [
            'work_id' => 999,
            'materials' => [
                [
                    'id' => 1,
                    'quantity' => 2
                ]
            ]
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('work_id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_requires_materials_array(): void
    {
        // Arrange
        $work = Work::factory()->create();

        $data = [
            'work_id' => $work->id
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('materials', $validator->errors()->toArray());
    }

    /** @test */
    public function it_requires_valid_material_ids(): void
    {
        // Arrange
        $work = Work::factory()->create();

        $data = [
            'work_id' => $work->id,
            'materials' => [
                [
                    'id' => 999,
                    'quantity' => 2
                ]
            ]
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('materials.0.id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_requires_positive_quantities(): void
    {
        // Arrange
        $work = Work::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'work_id' => $work->id,
            'materials' => [
                [
                    'id' => $material->id,
                    'quantity' => 0
                ]
            ]
        ];

        // Act
        $validator = validator($data, $this->request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('materials.0.quantity', $validator->errors()->toArray());
    }
}
