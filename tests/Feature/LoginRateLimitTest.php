<?php

use App\Models\User;

it('blocks login for 30 seconds after five incorrect attempts', function () {
    $user = User::factory()->create(['rol' => 'admin']);

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);
    }

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHas('login_wait_seconds', 30);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('window.setInterval(updateLoginCountdown, 1000)');

    $this->from(route('login'))
        ->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('login_wait_seconds');

    $this->travel(30)->seconds();

    $this->from(route('login'))
        ->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('admin.menu'));
});
