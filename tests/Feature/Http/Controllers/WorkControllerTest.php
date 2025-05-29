<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     public function test_can_change_state()
     {
         $response = $this->getJson('/api/v1/works/state/1/Cancelado');

            $response->assertStatus(200)
                ->assertJson([
                    'message' => "State changed successfully",
                    'data' => [
                        'id' => 1,
                        'state' => 'Cancelado'
                    ]
                ]);
     }
}
