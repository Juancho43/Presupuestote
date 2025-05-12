<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;

class AddMaterialsToInvoiceRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
