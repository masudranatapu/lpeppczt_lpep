<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginRouteTest extends TestCase
{
    public function test_login_post_route_exists_for_the_form_submission(): void
    {
        $this->assertNotNull(route('login.submit'));
    }
}
