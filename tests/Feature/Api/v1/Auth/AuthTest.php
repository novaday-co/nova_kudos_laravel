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

   public function test_user_can_confirm_otp_and_login()
   {
       $user = User::where('mobile', '09354068701')->firstOrFail();
       $this->postJson(route('v1.authentication.check.otp', $user->token), [
           'otp_code' => '842547'
       ])->assertOk();
       $this->assertDatabaseHas('users', ['otp_code' => '842547']);
   }

   public function test_invalid_otp_can_login()
   {
       $user = User::where('mobile', '09354068701')->firstOrFail();
       $this->postJson(route('v1.authentication.check.otp', $user->token), [
           'otp_code' => '129345'
       ])->assertUnauthorized();
   }

   public function test_user_can_get_new_otp_code()
   {
        $user = User::where('mobile', '09354068701')->firstOrFail();
        $this->postJson(route('v1.authentication.resend.otp', $user->token))->assertOk();
   }
}
