<?php

use App\Models\Cobros;
use App\Models\Inventario;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('calculates the cobro with inventory prices and decreases stock', function () {
    Storage::fake('public');

    $employee = User::factory()->create(['rol' => 'empleado']);
    $item = Inventario::create([
        'nombre_item' => 'Taladro',
        'categoria' => 'Herramientas',
        'stock_actual' => 5,
        'unidad' => 'unidades',
        'ubicacion' => 'Depósito',
        'estado' => 'normal',
        'precio' => 12.50,
    ]);

    $response = $this->actingAs($employee)
        ->from(route('cobros.empleado'))
        ->post(route('cobros.store'), [
            'nombre_cliente' => 'Cliente de prueba',
            'telefono' => '099123456',
            'concepto' => 'Venta de herramientas',
            'monto_base' => 5,
            'monto' => 1,
            'mano_de_obra' => 'si',
            'productos' => [
                ['inventario_id' => $item->id, 'cantidad' => 2],
                ['inventario_id' => $item->id, 'cantidad' => 1],
            ],
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('cobros', ['monto' => '42.50']);
    $this->assertDatabaseHas('inventarios', [
        'id' => $item->id,
        'stock_actual' => 2,
    ]);

    $cobro = Cobros::firstOrFail();

    expect($cobro->productos)->toBe([
        [
            'nombre_item' => 'Taladro',
            'cantidad' => 3,
            'precio_unitario' => '12.50',
            'subtotal' => '37.50',
        ],
    ]);
    expect($cobro->monto_mano_obra)->toBe('5.00');

    parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY), $whatsappQuery);

    expect($whatsappQuery['text'])
        ->toContain('*Productos:*')
        ->toContain('* Taladro x3 — $12.50 c/u = $37.50')
        ->toContain('*Monto de mano de obra:* $5.00')
        ->toContain('*MONTO TOTAL:* $42.50');

    $this->view('cobros.pdf', ['cobro' => $cobro])
        ->assertSee('Taladro')
        ->assertSee('Cantidad')
        ->assertSee('$12.50')
        ->assertSee('$37.50')
        ->assertSee('Monto de mano de obra:')
        ->assertSee('$5.00');
});

it('shows item breakdown and labor amount in the digital receipt', function () {
    $cobro = (object) [
        'id' => 1,
        'receipt_token' => 'test-token',
        'cantidad' => 42.50,
        'cliente' => (object) ['nombre' => 'Cliente de prueba'],
        'concepto' => 'Venta de herramientas',
        'fecha_de_pago' => now(),
        'empleado' => (object) ['name' => 'Empleado'],
        'productos' => [
            [
                'nombre_item' => 'Taladro',
                'cantidad' => 3,
                'precio_unitario' => '12.50',
                'subtotal' => '37.50',
            ],
        ],
        'monto_mano_obra' => '5.00',
    ];

    $this->view('recibos.show', ['cobro' => $cobro])
        ->assertSee('Taladro')
        ->assertSee('Cantidad: 3')
        ->assertSee('Precio unitario: $12.50')
        ->assertSee('Subtotal: $37.50')
        ->assertSee('Mano de obra')
        ->assertSee('$5.00');
});

it('rejects inventory quantities when stock is insufficient', function (int $stock, array $quantities) {
    $employee = User::factory()->create(['rol' => 'empleado']);
    $item = Inventario::create([
        'nombre_item' => 'Taladro',
        'categoria' => 'Herramientas',
        'stock_actual' => $stock,
        'unidad' => 'unidades',
        'ubicacion' => 'Depósito',
        'estado' => 'normal',
        'precio' => 12.50,
    ]);

    $this->actingAs($employee)
        ->from(route('cobros.empleado'))
        ->post(route('cobros.store'), [
            'nombre_cliente' => 'Cliente de prueba',
            'telefono' => '099123456',
            'concepto' => 'Venta de herramientas',
            'monto_base' => 5,
            'monto' => 5,
            'mano_de_obra' => 'si',
            'productos' => array_map(
                fn (int $quantity) => ['inventario_id' => $item->id, 'cantidad' => $quantity],
                $quantities
            ),
        ])
        ->assertRedirect(route('cobros.empleado'))
        ->assertSessionHasErrors([
            'productos' => 'No hay suficiente stock para Taladro.',
        ]);

    $this->assertDatabaseCount('cobros', 0);
    $this->assertDatabaseHas('inventarios', [
        'id' => $item->id,
        'stock_actual' => $stock,
    ]);
})->with([
    'item sin stock' => [0, [1]],
    'cantidad superior al stock' => [2, [3]],
    'cantidad duplicada superior al stock' => [3, [2, 2]],
]);

it('shows inventory products and their stock on the cobro form', function () {
    $employee = User::factory()->create(['rol' => 'empleado']);
    $item = Inventario::create([
        'nombre_item' => 'Tornillo',
        'categoria' => 'Ferretería',
        'stock_actual' => 4,
        'unidad' => 'unidades',
        'ubicacion' => 'Depósito',
        'estado' => 'normal',
        'precio' => 2.75,
    ]);

    $this->actingAs($employee)
        ->get(route('cobros.empleado'))
        ->assertSee('Agregar productos del inventario')
        ->assertSee('Tornillo')
        ->assertSee('2.75')
        ->assertSee('No hay suficiente stock.')
        ->assertSeeInOrder([
            'Datos del cobro',
            'toggle-inventory-products',
            'Concepto / Descripción',
            'Datos de la mano de obra',
            'monto_base',
        ]);
});
