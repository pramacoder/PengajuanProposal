<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_current_user_name_returns_pengguna_when_guest()
    {
        $this->assertEquals('Pengguna', UserHelper::getCurrentUserName());
    }

    public function test_get_current_user_name_returns_name_when_authenticated()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $this->actingAs($user);
        $this->assertEquals('John Doe', UserHelper::getCurrentUserName());
    }

    public function test_get_user_icon_returns_correct_icon_per_role()
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mhs);
        $this->assertEquals('fas fa-user-graduate', UserHelper::getUserIcon());
    }

    public function test_get_user_avatar_color_returns_correct_color()
    {
        $op = User::factory()->create(['role' => 'operator']);
        $this->actingAs($op);
        $this->assertEquals('warning', UserHelper::getUserAvatarColor());
    }

    public function test_get_user_profile_info_guest()
    {
        $info = UserHelper::getUserProfileInfo();
        $this->assertEquals('Pengguna', $info['name']);
        $this->assertEmpty($info['email']);
    }
}
