<?php

use App\Models\User;

it('blocks another employee creation for 10 seconds', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $employee = [
        'name' => 'Ana Empleada',
        'email' => 'ana.empleada@example.com',
        'password' => 'secret123',
    ];

    $this->actingAs($admin)
        ->from(route('admin.empleados.create'))
        ->post(route('admin.empleados.store'), $employee)
        ->assertRedirect(route('admin.menu'));

    $this->actingAs($admin)
        ->from(route('admin.empleados.create'))
        ->post(route('admin.empleados.store'), [
            ...$employee,
            'email' => 'otra.empleada@example.com',
        ])
        ->assertRedirect(route('admin.empleados.create'))
        ->assertSessionHas('employee_wait_seconds');

    $this->assertDatabaseCount('users', 2);

    $this->get(route('admin.empleados.create'))
        ->assertOk()
        ->assertSee('id="employee-create-button" type="submit" disabled', false)
        ->assertSee('window.setInterval(updateEmployeeCountdown, 1000)');

    $this->travel(10)->seconds();

    $this->actingAs($admin)
        ->from(route('admin.empleados.create'))
        ->post(route('admin.empleados.store'), [
            ...$employee,
            'email' => 'tercera.empleada@example.com',
        ])
        ->assertRedirect(route('admin.menu'));

    $this->assertDatabaseCount('users', 3);
});

it('requires an administrator to create an employee', function () {
    $this->post(route('admin.empleados.store'), [
        'name' => 'Ana Empleada',
        'email' => 'ana.empleada@example.com',
        'password' => 'secret123',
    ])->assertRedirect(route('login'));
});
