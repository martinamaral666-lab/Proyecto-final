<template>
  <div class="bg-slate-100 min-h-screen flex flex-col font-sans">
    <!-- ENCABEZADO SUPERIOR -->
    <header class="bg-[#044e39] text-white flex items-center justify-between px-6 py-4 shadow-lg z-30 sticky top-0">
      <div class="flex items-center gap-3">
        <span class="text-xl font-bold tracking-wide">GestiónCash</span>
      </div>
      <div>
        <Link :href="routes.empleadosIndex" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
          &larr; Volver al Panel
        </Link>
      </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
      <!-- MENÚ LATERAL -->


      <!-- CONTENIDO PRINCIPAL -->
      <main class="flex-1 overflow-y-auto p-8 flex items-center justify-center">
        <div class="max-w-xl w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">

          <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-slate-800">Editar Datos del Empleado</h1>
            <p class="text-xs text-slate-500 mt-1">Modifica la información de acceso o deja la contraseña en blanco si no deseas cambiarla.</p>
          </div>

          <!-- ERRORES -->
          <div v-if="Object.keys(form.errors).length > 0" class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
              <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
            </ul>
          </div>

          <!-- Formulario de Edición -->
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre Completo</label>
              <input type="text" v-model="form.name" required
                     class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
              <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
              <input type="email" v-model="form.email" required
                     class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
              <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nueva Contraseña <span class="text-slate-400 font-normal">(Opcional)</span></label>
              <input type="password" v-model="form.password" placeholder="••••••••"
                     class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
              <p class="text-[11px] text-slate-400 mt-1">Rellénalo solo si necesitas reestablecer la clave del empleado.</p>
              <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
              <Link :href="routes.empleadosIndex" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                Cancelar
              </Link>
              <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#044e39] hover:bg-[#033827] text-white text-sm font-semibold rounded-xl shadow-sm transition-all disabled:opacity-50">
                <span v-if="form.processing">Guardando...</span>
                <span v-else>Guardar Cambios</span>
              </button>
            </div>
          </form>

        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  empleado: { type: Object, required: true },
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  name: props.empleado.name || '',
  email: props.empleado.email || '',
  password: ''
})

const submitForm = () => {
  form.put(props.routes.empleadosUpdate || `/admin/empleados/${props.empleado.id}`)
}
</script>
