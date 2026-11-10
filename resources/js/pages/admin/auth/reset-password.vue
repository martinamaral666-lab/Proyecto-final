<template>
  <div class="bg-slate-100 min-h-screen flex items-center justify-center p-6 font-sans">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8">

        <!-- TITULO -->
        <div class="text-center mb-8">
          <h1 class="text-2xl font-bold text-[#044e39]">
            GestiónCash
          </h1>
          <h2 class="text-xl font-semibold text-slate-700 mt-4">
            Crear nueva contraseña
          </h2>
          <p class="text-sm text-slate-500 mt-2">
            Ingresá y confirmá tu nueva contraseña.
          </p>
        </div>

        <!-- ERRORES -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
          <ul class="list-disc list-inside">
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <!-- FORMULARIO -->
        <form @submit.prevent="submitForm" class="space-y-5">
          <!-- EMAIL -->
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
              Correo electrónico
            </label>
            <input
              type="email"
              v-model="form.email"
              id="email"
              required
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
            >
          </div>

          <!-- NUEVA CONTRASEÑA -->
          <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
              Nueva contraseña
            </label>
            <input
              type="password"
              v-model="form.password"
              id="password"
              minlength="8"
              required
              autocomplete="new-password"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
            >
            <p class="text-xs text-slate-400 mt-1">
              Debe tener al menos 8 caracteres.
            </p>
          </div>

          <!-- CONFIRMAR -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
              Confirmar nueva contraseña
            </label>
            <input
              type="password"
              v-model="form.password_confirmation"
              id="password_confirmation"
              minlength="8"
              required
              autocomplete="new-password"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
            >
          </div>

          <!-- BOTON -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition disabled:opacity-50"
          >
            <span v-if="form.processing">Actualizando...</span>
            <span v-else>Cambiar contraseña</span>
          </button>
        </form>

        <!-- VOLVER -->
        <div class="text-center mt-6">
          <button
            @click="volverLogin"
            type="button"
            class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline bg-transparent border-0 cursor-pointer"
          >
            Volver al inicio de sesión
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, required: true },
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

const submitForm = () => {
  form.post(props.routes.passwordUpdate || '/reset-password', {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}

const volverLogin = () => {
  router.post(props.routes.logout || '/logout')
}
</script>
