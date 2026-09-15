<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - GestiónCash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-emerald-900 text-white flex flex-col justify-between hidden md:flex">
            <div>

                <div class="px-6 py-5 text-xl font-bold tracking-wider border-b border-emerald-800">
                    GestiónCash
                </div>

                <div class="px-4 py-6 space-y-2">
                    <div class="text-xs font-semibold text-emerald-400 uppercase px-3 mb-2 tracking-wider">
                        Acciones Principales
                    </div>

                    <a href="{{ route('inventario.index') }}" class="flex items-center px-3 py-2.5 rounded-xl bg-emerald-800 text-white font-medium transition-colors">
                        Inventario General
                    </a>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">

           <header class="bg-emerald-900 shadow-sm h-16 flex items-center justify-between px-8 border-b border-emerald-800">
    <div class="flex items-center gap-4">
        <span class="font-semibold text-white">Panel Administrador</span>
    </div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
            Cerrar Sesión
        </button>
    </form>
</header>

            <!-- Contenido de la Página (Formulario) -->
            <main class="flex-1 p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6">Agregar Nuevo Ítem</h2>

                        <form action="{{ route('inventario.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Ítem</label>
                                    <input type="text" name="nombre_item" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                                    <input type="text" name="categoria" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Stock Actual</label>
                                    <input type="number" name="stock_actual" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Unidad</label>
                                    <input type="text" name="unidad" placeholder="Ej. Unidades, kg, metros" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Ubicación</label>
                                    <input type="text" name="ubicacion" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                                    <select name="estado" id="estadoSelect" onchange="cambiarColorEstado(this)" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-emerald-700">
                                        <option value="normal" class="text-emerald-700">Normal</option>
                                        <option value="critico" class="text-red-600">Crítico</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <a href="{{ route('inventario.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">Cancelar</a>
                                <button type="submit" class="px-4 py-2 bg-emerald-800 text-white rounded-lg hover:bg-emerald-900 transition-colors">Guardar Ítem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>

    <script>
    function cambiarColorEstado(select) {
        if (select.value === 'critico') {
            select.className = "w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-red-600 bg-red-50";
        } else {
            select.className = "w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-emerald-700 bg-emerald-50";
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        cambiarColorEstado(document.getElementById('estadoSelect'));
    });
    </script>
</body>
</html>
