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

    <div class="flex flex-1 overflow-hidden">

      <!-- CONTENIDO PRINCIPAL -->
      <main class="flex-1 overflow-y-auto p-8 flex items-center justify-center">
        <div class="max-w-xl w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">

          <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-slate-800">Editar Datos del Cliente</h1>
            <p class="text-xs text-slate-500 mt-1">Actualiza la información de contacto y datos principales del cliente.</p>
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
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Teléfono / WhatsApp</label>
              <input type="text" v-model="form.phone"
                     class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
              <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
              <input type="email" v-model="form.email"
                     class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-emerald-600 focus:bg-white transition-all">
              <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
              <Link href="/admin/infodeclientes" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
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
  cliente: { type: Object, required: true },
})

const form = useForm({
  name: props.cliente.name || '',
  phone: props.cliente.phone || '',
  email: props.cliente.email || ''
})


const submitForm = () => {
  form.put(`/admin/infodeclientes/${encodeURIComponent(props.cliente.id)}`)
}
</script>
