<template>
  <div class="bg-slate-100 min-h-screen flex flex-col font-sans">
    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
      <div class="flex items-center gap-3">
        <span class="text-xl font-bold tracking-wide">GestiónCash</span>
      </div>
      <div>
        <Link :href="routes.adminMenu" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
          &larr; Volver al Menú
        </Link>
      </div>
    </header>

    <div class="flex flex-1 overflow-hidden">


      <!-- CONTENIDO PRINCIPAL -->
      <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-5xl mx-auto">

          <!-- MENSAJE DE ÉXITO -->
          <div v-if="$page.props.flash?.success" class="mb-6 bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
            {{ $page.props.flash.success }}
          </div>

          <!-- Encabezado de la sección y botón de crear -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold text-slate-800">Panel de Empleados</h1>
              <p class="text-sm text-slate-500 mt-1">Personal registrado y accesos habilitados en el sistema.</p>
            </div>
            <div>
              <Link :href="routes.empleadosCreate" class="px-4 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                + Registrar Empleado
              </Link>
            </div>
          </div>

          <!-- Listado en formato de Tarjeta/Carpeta -->
          <div class="space-y-4">
            <template v-if="empleados && empleados.length > 0">
              <div v-for="empleado in empleados" :key="empleado.id" class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all hover:shadow-md">

                <!-- Información del empleado -->
                <div class="flex items-center gap-4">
                  <div class="space-y-0.5">
                    <div class="flex items-center gap-3">
                      <h3 class="font-bold text-slate-800 text-base">
                        {{ empleado.name }}
                      </h3>
                    </div>
                    <p class="text-xs text-slate-500">
                      Correo: <span class="text-slate-700 font-medium">{{ empleado.email }}</span>
                    </p>
                  </div>
                </div>

                <!-- Botones de Acción (Editar cuenta y Dar de baja) -->
                <div class="flex items-center gap-2">
                  <Link :href="`/admin/empleados/${empleado.id}/edit`"
                         class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl border border-slate-200 transition-all flex items-center gap-1.5">
                    Editar cuenta
                  </Link>

                  <button @click="eliminarEmpleado(empleado.id)" type="button"
                          class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-medium text-xs rounded-xl border border-rose-200 transition-all flex items-center gap-1.5">
                    Dar de baja
                  </button>
                </div>

              </div>
            </template>
            <template v-else>
              <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                No hay empleados registrados todavía. Haz clic en "Registrar Empleado" para agregar uno.
              </div>
            </template>
          </div>

        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  empleados: { type: Array, default: () => [] },
  routes: { type: Object, default: () => ({}) }
})

const eliminarEmpleado = (id) => {
  if (confirm('¿Estás seguro de dar de baja a este empleado?')) {
    router.delete(`/admin/empleados/${id}`)
  }
}
</script>
