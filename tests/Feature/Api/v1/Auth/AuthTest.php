<?php

namespace Tests\Feature\Api\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
   public function test_user_can_register_number()
   {
       $this->postJson(route('v1.authentication.login'), [
          'mobile' => '09354068701'
       ])->assertCreated();
       $this->assertDatabaseHas('users', ['mobile' => '09354068701']);
   }
}
