<?php

namespace Tests\Unit\Repository\V1;

use App\Enums\WorkStatus;
use App\Models\Budget;
use App\Models\Work;
use App\Repository\V1\WorkRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WorkRepositoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_create_creates_new_work()
    {
        $budget = Budget::factory()->create();
        $data = Mockery::mock(FormRequest::class);
        $data->shouldReceive('validated')->once();
        $data->shouldReceive('input')->with('order')->andReturn(1);
        $data->shouldReceive('input')->with('name')->andReturn('Test Work');
        $data->shouldReceive('input')->with('notes')->andReturn('Some notes');
        $data->shouldReceive('input')->with('estimated_time')->andReturn(10);
        $data->shouldReceive('input')->with('dead_line')->andReturn('2024-05-15');
        $data->shouldReceive('input')->with('cost', 0)->andReturn(1500.50);
        $data->shouldReceive('input')->with('status', WorkStatus::PRESUPUESTADO)->andReturn(WorkStatus::PRESUPUESTADO);
        $data->shouldReceive('input')->with('budget_id')->andReturn($budget->id);

        $repository = new WorkRepository();
        $work = $repository->create($data);

        $this->assertInstanceOf(Work::class, $work);
        $this->assertEquals(1, $work->order);
        $this->assertEquals('Test Work', $work->name);
        $this->assertEquals('Some notes', $work->notes);
        $this->assertEquals(10, $work->estimated_time);
        $this->assertEquals('2024-05-15', $work->dead_line->toDateString());
        $this->assertEquals(1500.50, $work->cost);
        $this->assertEquals(WorkStatus::PRESUPUESTADO, $work->status);
        $this->assertEquals(1, $work->budget_id);
    }
}
