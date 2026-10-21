<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('blocks receipt creation for 15 seconds after saving a charge', function () {
    Storage::fake('public');

    $employee = User::factory()->create(['rol' => 'empleado']);
    $charge = [
        'nombre_cliente' => 'Cliente de prueba',
        'telefono' => '099123456',
        'concepto' => 'Servicio',
        'monto' => 120,
        'mano_de_obra' => 'si',
    ];

    $this->actingAs($employee)
        ->from(route('cobros.empleado'))
        ->post(route('cobros.store'), $charge)
        ->assertRedirect();

    $this->assertDatabaseCount('cobros', 1);

    $this->actingAs($employee)
        ->from(route('cobros.empleado'))
        ->post(route('cobros.store'), [
            ...$charge,
            'nombre_cliente' => 'Segundo cliente',
        ])
        ->assertRedirect(route('cobros.empleado'))
        ->assertSessionHas('receipt_wait_seconds');

    $this->assertDatabaseCount('cobros', 1);

    $this->get(route('cobros.empleado'))
        ->assertOk()
        ->assertSee('id="receipt-submit-button" type="submit" class="btn-submit" disabled', false)
        ->assertSee('window.setInterval(updateReceiptCountdown, 1000)');

    $this->travel(15)->seconds();

    $this->actingAs($employee)
        ->from(route('cobros.empleado'))
        ->post(route('cobros.store'), [
            ...$charge,
            'nombre_cliente' => 'Tercer cliente',
        ])
        ->assertRedirect();

    $this->assertDatabaseCount('cobros', 2);
});

it('requires authentication to create a charge', function () {
    $this->post(route('cobros.store'), [
        'nombre_cliente' => 'Cliente de prueba',
        'telefono' => '099123456',
        'concepto' => 'Servicio',
        'monto' => 120,
        'mano_de_obra' => 'si',
    ])->assertRedirect(route('login'));
});
