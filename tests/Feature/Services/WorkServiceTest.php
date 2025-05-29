<?php

namespace test\Feature\Services;

use App\Models\Material;
use App\Models\Work;
use App\Repository\V1\WorkRepository;
use App\Services\V1\WorkService;
use Tests\TestCase;

class WorkServiceTest extends TestCase
{

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_AddMaterialsToWorkReturnsWork()
    {
        $mockRepository = Mockery::mock(WorkRepository::class);
        $mockRequest = Mockery::mock(FormRequest::class);

        $work = new Work();
        $work->materials = collect([
            (object)['quantity' => 2, 'price' => 10],
            (object)['quantity' => 1, 'price' => 20],
        ]);

        $mockRepository->shouldReceive('addMaterials')
            ->once()
            ->with($mockRequest)
            ->andReturn($work);

        $service = new WorkService($mockRepository);

        $result = $service->addMaterialsToWork($mockRequest);

        $this->assertInstanceOf(Work::class, $result);
        $this->assertEquals(40, $result->cost);
    }

    public function test_AddMaterialsToWorkHandlesException()
    {
        $mockRepository = Mockery::mock(WorkRepository::class);
        $mockRequest = Mockery::mock(FormRequest::class);

        $mockRepository->shouldReceive('addMaterials')
            ->once()
            ->with($mockRequest)
            ->andThrow(new \Exception('DB error'));

        $service = Mockery::mock(WorkService::class . '[errorResponse]', [$mockRepository]);
        $service->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('errorResponse')
            ->once()
            ->with(
                'Service Error: adding materials to work failed',
                'DB error',
                \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR
            )
            ->andReturn(new JsonResponse(['error' => 'DB error'], 500));

        $result = $service->addMaterialsToWork($mockRequest);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
    }
    public function test_calculateWorkCostReturnsSumOfMaterialCosts(): void
    {
        $repository = $this->createMock(WorkRepository::class);
        $service = new WorkService($repository);

        $work = new Work();
        $material1 = new Material();
        $material1->quantity = 2;
        $material1->price = 10;

        $material2 = new Material();
        $material2->quantity = 3;
        $material2->price = 20;

        $work->materials = collect([$material1, $material2]);

        $cost = $service->calculateWorkCost($work);

        $this->assertEquals(80, $cost);
    }

    public function test_calculateWorkCostReturnsZeroForWorkWithoutMaterials(): void
    {
        $repository = $this->createMock(WorkRepository::class);
        $service = new WorkService($repository);

        $work = new Work();
        $work->materials = collect([]);

        $cost = $service->calculateWorkCost($work);

        $this->assertEquals(0, $cost);
    }

    public function test_calculateWorkCostHandlesNullQuantityAndPriceAsZero(): void
    {
        $repository = $this->createMock(WorkRepository::class);
        $service = new WorkService($repository);

        $work = new Work();
        $material = new Material();
        $material->quantity = null;
        $material->price = null;

        $work->materials = collect([$material]);

        $cost = $service->calculateWorkCost($work);

        $this->assertEquals(0, $cost);
    }
}
