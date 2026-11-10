<template>
  <div class="bg-slate-100 font-sans antialiased min-h-screen flex">
    <!-- BARRA LATERAL -->
    <aside class="w-64 bg-emerald-900 text-white flex flex-col justify-between hidden md:flex shrink-0">
      <div>
        <div class="px-6 py-5 text-xl font-bold tracking-wider border-b border-emerald-800">
          GestiónCash
        </div>

        <div class="px-4 py-6 space-y-2">
          <div class="text-xs font-semibold text-emerald-400 uppercase px-3 mb-2 tracking-wider">
            Acciones Principales
          </div>

          <Link :href="routes.adminMenu" class="flex items-center px-3 py-2.5 rounded-xl bg-emerald-800 text-white font-medium transition-colors">
            Inventario General
          </Link>
        </div>
      </div>
    </aside>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      <!-- ENCABEZADO -->
      <header class="bg-emerald-900 shadow-sm h-16 flex items-center justify-between px-8 border-b border-emerald-800">
        <div class="flex items-center gap-4">
          <span class="font-semibold text-white">Panel Administrador</span>
        </div>
        <button @click="cerrarSesion" type="button" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
          Cerrar Sesión
        </button>
      </header>

      <!-- FORMULARIO -->
      <main class="flex-1 p-8">
        <div class="max-w-4xl mx-auto">
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Agregar Nuevo Ítem</h2>

            <form @submit.prevent="submitForm" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Ítem</label>
                  <input type="text" v-model="form.nombre_item" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                  <div v-if="form.errors.nombre_item" class="text-red-500 text-xs mt-1">{{ form.errors.nombre_item }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                  <input type="text" v-model="form.categoria" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                  <div v-if="form.errors.categoria" class="text-red-500 text-xs mt-1">{{ form.errors.categoria }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Stock Actual</label>
                  <input type="number" v-model="form.stock_actual" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                  <div v-if="form.errors.stock_actual" class="text-red-500 text-xs mt-1">{{ form.errors.stock_actual }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Unidad</label>
                  <input type="text" v-model="form.unidad" placeholder="Ej. Unidades, kg, metros" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                  <div v-if="form.errors.unidad" class="text-red-500 text-xs mt-1">{{ form.errors.unidad }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Ubicación</label>
                  <input type="text" v-model="form.ubicacion" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                  <div v-if="form.errors.ubicacion" class="text-red-500 text-xs mt-1">{{ form.errors.ubicacion }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Precio</label>
                  <input type="number" v-model="form.precio" step="1" class="w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                  <div v-if="form.errors.precio" class="text-red-500 text-xs mt-1">{{ form.errors.precio }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                  <select v-model="form.estado" :class="estadoClass">
                    <option value="normal" class="text-emerald-700">Normal</option>
                    <option value="critico" class="text-red-600">Crítico</option>
                  </select>
                  <div v-if="form.errors.estado" class="text-red-500 text-xs mt-1">{{ form.errors.estado }}</div>
                </div>
              </div>

              <div class="flex justify-end gap-3 mt-6">
                <Link :href="routes.inventarioIndex" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">
                  Cancelar
                </Link>
                <button type="submit" :disabled="form.processing || remainingSeconds > 0" class="px-4 py-2 bg-emerald-800 text-white rounded-lg hover:bg-emerald-900 transition-colors disabled:cursor-not-allowed disabled:opacity-60">
                  <span v-if="remainingSeconds > 0">Espera {{ remainingSeconds }}s</span>
                  <span v-else-if="form.processing">Guardando...</span>
                  <span v-else>Guardar Ítem</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  saveWaitSeconds: { type: Number, default: 0 },
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  nombre_item: '',
  categoria: '',
  stock_actual: '',
  unidad: '',
  ubicacion: '',
  precio: '',
  estado: 'normal'
})

const remainingSeconds = ref(props.saveWaitSeconds)
let countdown = null

onMounted(() => {
  if (remainingSeconds.value > 0) {
    countdown = setInterval(() => {
      if (remainingSeconds.value <= 0) {
        clearInterval(countdown)
      } else {
        remainingSeconds.value -= 1
      }
    }, 1000)
  }
})

onUnmounted(() => {
  if (countdown) clearInterval(countdown)
})

const estadoClass = computed(() => {
  return form.estado === 'critico'
    ? "w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-red-600 bg-red-50"
    : "w-full rounded-lg border-slate-300 border px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-emerald-700 bg-emerald-50"
})

const submitForm = () => {
  form.post(props.routes.inventarioStore || '/menu/admin/inventario')
}

const cerrarSesion = () => {
  router.post(props.routes.logout || '/logout')
}
</script>
