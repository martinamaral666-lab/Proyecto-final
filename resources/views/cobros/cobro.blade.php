<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cobro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: sans-serif; }
        body { background: #009966; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px 0; position: relative; }
        .card-cobro { background: #fff; padding: 30px; border-radius: 16px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,.15); }
        .card-header { text-align: center; margin-bottom: 20px; }
        .card-header h2 { color: #0f172a; font-size: 1.4rem; margin-bottom: 4px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #334155; }
        .form-control { width: 100%; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e0; border-radius: 8px; outline: none; font-size: 0.95rem; }
        .btn-submit { width: 100%; padding: 12px; background: #009966; color: #fff; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; margin-top: 10px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; }
    </style>


</head>
<body>

<div class="flex items-center gap-6">
            <form method="POST" action="{{ route('logout') }}" style="position: absolute; top: 20px; right: 20px;">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all">
                    Cerrar Sesión
                </button>
            </form>
        </div>

<div class="card-cobro">
    <div class="card-header">
        <h2>Registrar Cobro</h2>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cobros.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nombre_cliente">Nombre del Cliente</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono (WhatsApp)</label>
            <input type="text" id="telefono" name="telefono" class="form-control" placeholder="099123456" required>
        </div>

        <div class="form-group">
            <label for="concepto">Concepto / Descripción</label>
            <input type="text" id="concepto" name="concepto" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="monto">Monto / Cantidad ($)</label>
            <input type="number" id="monto" name="monto" class="form-control" step="0.01" min="0" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label for="mano_de_obra">¿Se realizó la mano de obra?</label>
            <select id="mano_de_obra" name="mano_de_obra" class="form-control" required>
                <option value="si">Sí</option>
                <option value="no">No</option>
            </select>
        </div>

        <div class="form-group">
            <label for="motivo_no_realizado">Motivo (si fue "No")</label>
            <input type="text" id="motivo_no_realizado" name="motivo_no_realizado" class="form-control">
        </div>

        <button type="submit" class="btn-submit">Guardar y Generar Recibo</button>
    </form>
</div>

</body>
</html>
