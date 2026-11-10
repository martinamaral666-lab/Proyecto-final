<template>
  <div class="bg-slate-100 min-h-screen flex flex-col font-sans">
    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
      <div class="flex items-center gap-3">
        <span class="text-xl font-bold tracking-wide">GestiónCash</span>
      </div>
      <div>
        <Link href="/admin/infodeclientes" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
          &larr; Volver al Directorio
        </Link>
      </div>
    </header>

    <main class="flex-1 p-8">
      <div class="max-w-3xl mx-auto space-y-6">

        <!-- DATOS PRINCIPALES -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ cliente.nombre || cliente.name }}</h1>
            <p class="text-sm text-slate-500 mt-1">Ficha detallada del cliente</p>
          </div>

          <span v-if="cliente.total_compras >= 3" class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
            Habitual
          </span>
          <span v-else class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
            Ocasional
          </span>
        </div>

        <!-- INFORMACIÓN DE CONTACTO -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
          <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wide pb-3 border-b border-slate-100 mb-4">
            Información de contacto
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p class="text-xs text-slate-400">Teléfono / WhatsApp</p>
              <p class="font-semibold text-slate-800">{{ cliente.telefono || cliente.phone || 'No registrado' }}</p>
            </div>
            <div>
              <p class="text-xs text-slate-400">Correo Electrónico</p>
              <p class="font-semibold text-slate-800">{{ cliente.email || 'No registrado' }}</p>
            </div>
          </div>
        </div>

        <!-- HISTORIAL DE ACTIVIDAD -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
          <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wide pb-3 border-b border-slate-100 mb-4">
            Historial de actividad reciente
          </h2>

          <div v-if="cobros.length === 0" class="text-center text-sm text-slate-400 py-8">
            Este cliente todavía no tiene cobros registrados.
          </div>

          <div v-else class="divide-y divide-slate-100">
            <div v-for="cobro in cobros" :key="cobro.id" class="py-4 flex items-start justify-between gap-4">
              <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-800">
                    {{ cobro.tipo_registro === 'venta' ? 'Venta en caja' : 'Mano de obra' }}
                  </span>

                  <template v-if="cobro.tipo_registro === 'mano_obra'">
                    <span v-if="cobro.mano_de_obra === 'si'" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-green-100 text-green-800">
                      Realizada
                    </span>
                    <span v-else class="px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-800">
                      No realizada ({{ cobro.motivo_no_realizado || 'Sin motivo' }})
                    </span>
                  </template>

                  <span class="text-xs text-slate-400">{{ formatFecha(cobro.created_at) }} hs</span>
                </div>

                <p class="text-sm text-slate-700">Concepto: {{ cobro.concepto }}</p>

                <ul v-if="cobro.productos && cobro.productos.length" class="text-xs text-slate-500 space-y-0.5">
                  <li v-for="(producto, i) in cobro.productos" :key="i">
                    {{ producto.nombre_item }} x{{ producto.cantidad }}
                    — ${{ Number(producto.subtotal).toFixed(2) }}
                  </li>
                </ul>

                <p class="text-xs text-slate-400">
                  Pago: {{ cobro.metodo_pago === 'efectivo' ? 'Efectivo' : 'Tarjeta' }}
                </p>
              </div>

              <div class="text-right shrink-0">
                <p class="text-lg font-extrabold text-[#064e3b]">${{ Number(cobro.monto).toFixed(2) }}</p>
                <a :href="`/cobros/${cobro.id}/pdf`" target="_blank" class="text-xs font-semibold text-emerald-700 hover:underline">
                  Ver PDF
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- ACCIONES -->
        <div class="flex items-center justify-end gap-4">
          <Link :href="`/admin/infodeclientes/${encodeURIComponent(cliente.nombre || cliente.name)}/edit`"
                class="px-4 py-2.5 text-sm font-semibold text-slate-700 hover:text-slate-900">
            Editar este cliente
          </Link>

          <Link href="/admin/infodeclientes"
                class="px-5 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
            Volver al listado
          </Link>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  cliente: { type: Object, required: true },
  cobros: { type: Array, default: () => [] },
})

const formatFecha = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('es-UY', {
    timeZone: 'America/Montevideo',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  }).replace(',', '')
}
</script>
