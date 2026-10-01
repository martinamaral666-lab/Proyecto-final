<?php

use App\Models\User;

it('blocks another inventory save for 15 seconds', function () {
    $admin = User::factory()->create(['rol' => 'admin']);
    $item = [
        'nombre_item' => 'Taladro',
        'categoria' => 'Herramientas',
        'stock_actual' => 4,
        'unidad' => 'unidades',
        'ubicacion' => 'Depósito',
        'estado' => 'normal',
    ];

    $this->actingAs($admin)
        ->from(route('inventario.crear'))
        ->post(route('inventario.store'), $item)
        ->assertRedirect(route('inventario.index'));

    $this->actingAs($admin)
        ->from(route('inventario.crear'))
        ->post(route('inventario.store'), $item)
        ->assertRedirect(route('inventario.crear'))
        ->assertSessionHas('save_wait_seconds');

    $this->assertDatabaseCount('inventarios', 1);

    $this->get(route('inventario.crear'))
        ->assertOk()
        ->assertSee('id="inventory-save-button" type="submit" disabled', false)
        ->assertSee('Espera', false)
        ->assertSee('window.setInterval(updateSaveCountdown, 1000)');

    $this->travel(15)->seconds();

    $this->actingAs($admin)
        ->from(route('inventario.crear'))
        ->post(route('inventario.store'), $item)
        ->assertRedirect(route('inventario.index'));

    $this->assertDatabaseCount('inventarios', 2);
});
