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
            Recuperar contraseña
          </h2>
          <p class="text-sm text-slate-500 mt-2">
            Ingresá tu correo electrónico y te enviaremos un enlace para crear una nueva contraseña.
          </p>
        </div>

        <!-- MENSAJE DE ESTADO -->
        <div v-if="$page.props.flash?.status" class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
          {{ $page.props.flash.status }}
        </div>

        <!-- ERRORES -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
          <ul class="list-disc list-inside">
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <!-- FORMULARIO -->
        <form @submit.prevent="submitForm" class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
              Correo electrónico
            </label>
            <input
              type="email"
              v-model="form.email"
              id="email"
              required
              autofocus
              placeholder="ejemplo@gmail.com"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
            >
          </div>

          <button
            type="submit"
            :disabled="form.processing || remainingSeconds > 0"
            class="w-full bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition disabled:cursor-not-allowed disabled:opacity-60"
          >
            <span v-if="remainingSeconds > 0">Espera {{ remainingSeconds }}s</span>
            <span v-else-if="form.processing">Enviando...</span>
            <span v-else>Enviar enlace</span>
          </button>
        </form>

        <!-- VOLVER / LOGOUT -->
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
import { ref, onMounted, onUnmounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  resetWaitSeconds: { type: Number, default: 0 },
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  email: ''
})

const remainingSeconds = ref(props.resetWaitSeconds)
let countdown = null

onMounted(() => {
  if (remainingSeconds.value > 0) {
    countdown = setInterval(() => {
      remainingSeconds.value -= 1
      if (remainingSeconds.value <= 0) {
        clearInterval(countdown)
      }
    }, 1000)
  }
})

onUnmounted(() => {
  if (countdown) clearInterval(countdown)
})

const submitForm = () => {
  form.post(props.routes.passwordEmail || '/forgot-password', {
    onFinish: () => {
      // Opcional si deseas mantener sincronizado algún estado
    }
  })
}

const volverLogin = () => {
  router.post(props.routes.logout || '/logout')
}
</script>
