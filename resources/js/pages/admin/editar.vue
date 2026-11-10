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

    <main class="flex-1 p-8">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
          <h1 class="text-2xl font-bold text-slate-800 mb-6">Editar ítem de inventario</h1>

          <!-- ERRORES -->
          <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
              <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
            </ul>
          </div>

          <form @submit.prevent="guardar" class="space-y-5">
            <div>
              <label for="nombre_item" class="block text-sm font-semibold text-slate-700 mb-2">Nombre del ítem</label>
              <input id="nombre_item" v-model="form.nombre_item" type="text" required
                     class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="categoria" class="block text-sm font-semibold text-slate-700 mb-2">Categoría</label>
                <input id="categoria" v-model="form.categoria" type="text"
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
              </div>

              <div>
                <label for="unidad" class="block text-sm font-semibold text-slate-700 mb-2">Unidad</label>
                <input id="unidad" v-model="form.unidad" type="text"
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
              </div>

              <div>
                <label for="stock_actual" class="block text-sm font-semibold text-slate-700 mb-2">Stock actual</label>
                <input id="stock_actual" v-model="form.stock_actual" type="number" min="0" step="any" required
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
              </div>

              <div>
                <label for="precio" class="block text-sm font-semibold text-slate-700 mb-2">Precio</label>
                <input id="precio" v-model="form.precio" type="number" min="0" step="any" required
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
              </div>

              <div>
                <label for="ubicacion" class="block text-sm font-semibold text-slate-700 mb-2">Ubicación</label>
                <input id="ubicacion" v-model="form.ubicacion" type="text"
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600">
              </div>

              <div>
                <label for="estado" class="block text-sm font-semibold text-slate-700 mb-2">Estado</label>
                <select id="estado" v-model="form.estado"
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600 bg-white">
                  <option value="normal">Normal</option>
                  <option value="critico">Crítico</option>
                  <option value="en uso">En uso</option>
                </select>
              </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
              <button type="submit" :disabled="form.processing"
                      class="bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition disabled:opacity-50">
                <span v-if="form.processing">Guardando...</span>
                <span v-else>Guardar cambios</span>
              </button>

              <Link href="/menu/admin" class="px-6 py-3 text-sm font-semibold text-slate-600 hover:text-slate-800">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  item: { type: Object, required: true },
})

const form = useForm({
  nombre_item: props.item.nombre_item ?? '',
  categoria: props.item.categoria ?? '',
  stock_actual: props.item.stock_actual ?? 0,
  unidad: props.item.unidad ?? '',
  ubicacion: props.item.ubicacion ?? '',
  precio: props.item.precio ?? 0,
  estado: (props.item.estado ?? 'normal').toLowerCase(),
})

const guardar = () => {
  form.put(`/inventario/${props.item.id}`)
}
</script>
