<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cobros - GestiónCash</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif; }
        body { background: #f1f5f9; color: #1e293b; min-height: 100vh; }

        /* Navbar */
        .navbar { background: #064e3b; color: #fff; padding: 15px 24px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .btn-back { background: rgba(255, 255, 255, 0.15); color: #fff; text-decoration: none; padding: 8px 12px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; }
        .btn-back:hover { background: rgba(255, 255, 255, 0.25); }
        .brand-title { font-size: 1.25rem; font-weight: 700; }

        /* Contenedor Principal */
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }

        /* Header Sección */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .section-header h2 { font-size: 1.5rem; color: #0f172a; font-weight: 700; }

        /* Tarjeta Contenedora de Cobros */
        .card-table { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; border: 1px solid #e2e8f0; }

        /* Lista de Cobros */
        .cobro-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .cobro-item:last-child { border-bottom: none; }
        .cobro-item:hover { background: #f8fafc; }

        /* Info Izquierda */
        .cobro-info { display: flex; flex-direction: column; gap: 4px; }
        .cliente-name { font-weight: 700; color: #0f172a; font-size: 1rem; }
        .cliente-phone { font-weight: 400; color: #64748b; font-size: 0.85rem; margin-left: 6px; }
        .concepto-text { color: #475569; font-size: 0.9rem; }
        .date-text { color: #94a3b8; font-size: 0.78rem; margin-top: 2px; }

        /* Estado Badge */
        .badge { display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; margin-top: 4px; width: fit-content; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        /* Info Derecha */
        .cobro-actions { display: flex; align-items: center; gap: 20px; }
        .monto-text { font-size: 1.15rem; font-weight: 800; color: #064e3b; text-align: right; }
        .btn-pdf { background: #f1f5f9; color: #334155; text-decoration: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; border: 1px solid #cbd5e0; transition: all 0.2s; }
        .btn-pdf:hover { background: #064e3b; color: #fff; border-color: #064e3b; }

        /* Estado Vacío */
        .empty-state { text-align: center; padding: 40px 20px; color: #64748b; font-size: 0.95rem; }

        /* Paginación */
        .pagination-wrapper { margin-top: 20px; display: flex; justify-content: center; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="{{ route('admin.menu') }}" class="btn-back">← Volver</a>
            <span class="brand-title">GestiónCash</span>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="section-header">
            <h2>Historial de Cobros</h2>
        </div>

        <div class="card-table">
            @forelse ($cobros as $cobro)
                <div class="cobro-item">
                    <div class="cobro-info">
                        <div>
                            <span class="cliente-name">{{ $cobro->nombre_cliente }}</span>
                            <span class="cliente-phone">({{ $cobro->telefono }})</span>
                        </div>
                        <span class="concepto-text">Concepto: {{ $cobro->concepto }}</span>

                        @if($cobro->mano_de_obra === 'si')
                            <span class="badge badge-success">Mano de obra: Realizada</span>
                        @else
                            <span class="badge badge-danger">Mano de obra: No realizada ({{ $cobro->motivo_no_realizado ?? 'Sin motivo' }})</span>
                        @endif

                        <span class="date-text">{{ $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i') }} hs</span>
                    </div>

                    <div class="cobro-actions">
                        <span class="monto-text">${{ number_format($cobro->monto, 2) }}</span>
                        <a href="{{ asset('storage/recibos/recibo_' . $cobro->id . '.pdf') }}" target="_blank" class="btn-pdf">Ver PDF</a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    No hay registros de cobros disponibles.
                </div>
            @endforelse
        </div>

        <!-- PAGINACIÓN -->
        <div class="mt-4">
            {{ $cobros->links() }}
        </div>

    </main>

</body>
</html>
