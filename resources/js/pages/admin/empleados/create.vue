<template>
  <div class="bg-[#044e39] font-sans antialiased flex justify-center items-center min-h-screen m-0 relative">
    <!-- NAVBAR -->
    <nav class="absolute top-0 w-full h-[40px] text-white px-6 py-4 flex items-center justify-between z-10">
      <div class="flex items-center gap-3">
        <Link href="/menu/admin" class="bg-white/15 hover:bg-white/25 text-white text-sm font-semibold px-3 py-2 rounded-lg transition inline-flex items-center">
          &larr; Volver
        </Link>
        <img src="/imagenes/logo.png" alt="Logo GestiónCash" class="w-[38px] h-[38px] object-contain rounded-[10px] bg-white/10 p-1">
        <span class="text-xl font-bold tracking-wide">GestiónCash</span>
      </div>
    </nav>

    <!-- TARJETA -->
    <div class="bg-white w-full max-w-[400px] p-[30px] rounded-[16px] shadow-xl">
      <div class="text-center mb-6">
        <img src="/imagenes/logo.png" alt="Logo GestiónCash" class="w-[84px] h-[84px] object-contain mx-auto mb-3 bg-[#ecfdf5] rounded-[18px] p-2.5">
        <h2 class="m-0 text-slate-900 text-xl font-bold">Registrar Nuevo Empleado</h2>
      </div>

      <!-- ERRORES -->
      <div v-if="Object.keys(form.errors).length > 0" class="text-red-700 bg-red-50 border border-red-300 p-3 mb-4 rounded-lg text-sm">
        <ul class="m-0 pl-4 list-disc">
          <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <!-- ÉXITO -->
      <div v-if="$page.props.flash?.success" class="text-emerald-800 bg-emerald-50 border border-emerald-300 p-3 mb-4 rounded-lg text-sm text-center">
        {{ $page.props.flash.success }}
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label for="name" class="block mb-1.5 text-slate-700 text-sm font-semibold">Nombre Completo</label>
          <input type="text" id="name" v-model="form.name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#044e39]" required>
        </div>

        <div>
          <label for="email" class="block mb-1.5 text-slate-700 text-sm font-semibold">Correo Electrónico</label>
          <input type="email" id="email" v-model="form.email" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#044e39]" required>
        </div>

        <div>
          <label for="password" class="block mb-1.5 text-slate-700 text-sm font-semibold">Contraseña</label>
          <input type="password" id="password" v-model="form.password" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#044e39]" required>
        </div>

        <div>
          <label for="password_confirmation" class="block mb-1.5 text-slate-700 text-sm font-semibold">Confirmar Contraseña</label>
          <input type="password" id="password_confirmation" v-model="form.password_confirmation" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#044e39]" required>
        </div>

        <button type="submit" :disabled="form.processing || remainingSeconds > 0" class="w-full py-3 bg-[#044e39] hover:bg-[#033827] text-white font-semibold rounded-lg text-base cursor-pointer mt-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
          <span v-if="remainingSeconds > 0">Espera {{ remainingSeconds }}s</span>
          <span v-else-if="form.processing">Creando...</span>
          <span v-else>Crear Empleado</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  employeeWaitSeconds: { type: Number, default: 0 },
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const remainingSeconds = ref(props.employeeWaitSeconds)
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

const submitForm = () => {
  form.post('/admin/empleados')
}
</script>
