<?php

namespace Tests\Feature\Traits;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait WithAuthentication
{
    protected function authenticateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }
}
