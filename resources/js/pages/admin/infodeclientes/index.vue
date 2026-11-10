<template>
  <div class="bg-slate-100 min-h-screen flex flex-col font-sans">
    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
      <div class="flex items-center gap-3">
        <span class="text-xl font-bold tracking-wide">GestiónCash</span>
      </div>
      <div>
        <Link href="/menu/admin" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
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

          <!-- Encabezado de la sección -->
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Directorio de Clientes</h2>
            <p class="text-sm text-slate-500">Información de contacto y registro de clientes del sistema.</p>
          </div>

          <!-- BUSCADOR -->
          <form @submit.prevent="buscar" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row items-center gap-3 mb-6">
            <div class="w-full relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </span>
              <input type="text" v-model="searchQuery" placeholder="Buscar cliente, teléfono o correo..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-[#044e39] text-sm">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
              <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">Buscar</button>
              <button v-if="filters.search" type="button" @click="limpiar" class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">Limpiar</button>
            </div>
          </form>

          <!-- LISTADO DE CLIENTES -->
          <template v-if="clientes && clientes.length > 0">
            <div v-for="cliente in clientes" :key="cliente.id || cliente.nombre" class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-4 flex items-center justify-between">

              <!-- Info del Cliente (Lado Izquierdo) -->
              <div class="space-y-1">
                <div class="flex items-center gap-3 flex-wrap">
                  <h3 class="font-bold text-slate-800 text-base">
                    {{ cliente.nombre }}
                  </h3>
                  <span class="text-xs text-slate-400 font-medium">
                    ({{ cliente.telefono ?? 'Sin teléfono' }})
                  </span>

                  <span v-if="cliente.total_compras >= 3" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                    Habitual ({{ cliente.total_compras }} compras)
                  </span>
                  <span v-else class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                    Ocasional ({{ cliente.total_compras }})
                  </span>
                </div>

                <p class="text-xs text-slate-500">
                  Email: <span class="font-medium text-slate-700">{{ cliente.email ?? 'No registrado' }}</span>
                </p>
              </div>

              <!-- Botones de Acción (Lado Derecho) -->
              <div class="flex items-center gap-2">
                <Link :href="`/admin/infodeclientes/${encodeURIComponent(cliente.nombre)}`"
                      class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-medium text-xs rounded-xl border border-emerald-200 transition-all flex items-center gap-1.5">
                  Ver Detalladamente
                </Link>

                <Link :href="`/admin/infodeclientes/${encodeURIComponent(cliente.nombre)}/edit`"
                      class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl border border-slate-200 transition-all flex items-center gap-1.5">
                  Editar
                </Link>
              </div>

            </div>
          </template>
          <template v-else>
            <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
              {{ filters.search ? 'No se encontraron clientes para esa búsqueda.' : 'Todavía no hay clientes habituales (3 o más compras en el mes).' }}
            </div>
          </template>

        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  clientes: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({ search: '' }) },
})

const searchQuery = ref(props.filters?.search ?? '')

const buscar = () => {
  router.get('/admin/infodeclientes', { search: searchQuery.value || undefined }, { preserveState: true })
}

const limpiar = () => {
  searchQuery.value = ''
  buscar()
}
</script>
