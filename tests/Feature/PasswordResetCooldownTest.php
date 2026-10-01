<?php

use App\Models\User;
use App\Notifications\CustomResetPassword;
use Illuminate\Support\Facades\Notification;

it('waits 30 seconds between password reset link requests', function () {
    Notification::fake();

    $admin = User::factory()->create(['rol' => 'admin']);
    $user = User::factory()->create();
    $requestData = ['email' => $user->email];

    $this->actingAs($admin)
        ->from(route('password.request'))
        ->post(route('password.email'), $requestData)
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('reset_wait_seconds', 30);

    Notification::assertSentToOnce($user, CustomResetPassword::class);

    $this->actingAs($admin)
        ->from(route('password.request'))
        ->post(route('password.email'), $requestData)
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('reset_wait_seconds');

    Notification::assertSentToOnce($user, CustomResetPassword::class);

    $this->actingAs($admin)
        ->get(route('password.request'))
        ->assertOk()
        ->assertSee('id="reset-link-button"', false)
        ->assertSee('disabled', false)
        ->assertSee('window.setInterval(updateResetCountdown, 1000)');

    $this->travel(30)->seconds();

    $this->actingAs($admin)
        ->from(route('password.request'))
        ->post(route('password.email'), $requestData)
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('reset_wait_seconds', 30);

    Notification::assertSentToTimes($user, CustomResetPassword::class, 2);
});

it('redirects guests away from password recovery', function () {
    $this->get(route('password.request'))
        ->assertRedirect(route('login'));

    $this->post(route('password.email'), ['email' => 'user@example.com'])
        ->assertRedirect(route('login'));
});

it('forbids employees from password recovery', function () {
    $employee = User::factory()->create(['rol' => 'empleado']);

    $this->actingAs($employee)
        ->get(route('password.request'))
        ->assertForbidden();

    $this->actingAs($employee)
        ->post(route('password.email'), ['email' => $employee->email])
        ->assertForbidden();
});

it('logs out from the return links on both password pages', function () {
    $admin = User::factory()->create(['rol' => 'admin']);

    $this->actingAs($admin)
        ->get(route('password.request'))
        ->assertOk()
        ->assertSee('method="POST"', false)
        ->assertSee(route('logout'), false);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();

    $this->actingAs($admin)
        ->get(route('password.reset', [
            'token' => 'test-token',
            'email' => $admin->email,
        ]))
        ->assertOk()
        ->assertSee('method="POST"', false)
        ->assertSee(route('logout'), false);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
