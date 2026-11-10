<template>
  <div class="bg-slate-100 min-h-screen font-sans">
    <!-- HEADER -->
    <header class="bg-[#044e39] text-white px-6 py-4 shadow-lg">
      <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold">GestiónCash</h1>
          <p class="text-emerald-200 text-sm">Mi cuenta</p>
        </div>

        <Link
          :href="routes.adminMenu"
          class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-sm transition"
        >
          Volver
        </Link>
      </div>
    </header>

    <!-- CONTENIDO -->
    <main class="max-w-4xl mx-auto p-6">
      <!-- MENSAJE DE ÉXITO -->
      <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
        {{ $page.props.flash.success }}
      </div>

      <!-- ERRORES GENERALES -->
      <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
          <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <!-- TARJETA -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <!-- TITULO -->
        <div class="mb-8">
          <h2 class="text-2xl font-bold text-slate-800">
            Configuración de cuenta
          </h2>
          <p class="text-slate-500 mt-1">
            Actualizá tus datos personales y contraseña.
          </p>
        </div>

        <!-- FORMULARIO -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- NOMBRE -->
          <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
              Nombre
            </label>
            <input
              type="text"
              id="name"
              :value="usuario.name"
              disabled
              class="w-full px-4 py-3 rounded-lg border border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed"
            >
            <p class="text-xs text-slate-400 mt-1">
              El nombre no puede modificarse desde esta sección.
            </p>
          </div>

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
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- TELEFONO -->
          <div>
            <label for="telefono" class="block text-sm font-semibold text-slate-700 mb-2">
              Teléfono
            </label>
            <input
              type="text"
              v-model="form.telefono"
              id="telefono"
              maxlength="20"
              placeholder="Ej: 099 123 456"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
            >
            <div v-if="form.errors.telefono" class="text-red-500 text-xs mt-1">{{ form.errors.telefono }}</div>
          </div>

          <!-- SECCION CONTRASEÑA -->
          <div class="border-t border-slate-200 pt-6">
            <h3 class="text-lg font-bold text-slate-800 mb-2">
              Cambiar contraseña
            </h3>
            <p class="text-sm text-slate-500 mb-4">
              Para cambiar tu contraseña, ingresá primero la contraseña actual.
            </p>

            <div class="space-y-4">
              <!-- CONTRASEÑA ACTUAL -->
              <div>
                <label for="password_actual" class="block text-sm font-semibold text-slate-700 mb-2">
                  Contraseña actual
                </label>
                <div class="relative">
                  <input
                    type="password"
                    v-model="form.password_actual"
                    id="password_actual"
                    autocomplete="current-password"
                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  >
                  <button
                    type="button"
                    @click="togglePassword('password_actual')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                    aria-label="Mostrar contraseña"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <div v-if="form.errors.password_actual" class="text-red-500 text-xs mt-1">{{ form.errors.password_actual }}</div>
                <p class="text-xs text-slate-400 mt-1">
                  Es necesario ingresar la contraseña actual para establecer una nueva.
                </p>
              </div>

              <!-- NUEVA CONTRASEÑA -->
              <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                  Nueva contraseña
                </label>
                <div class="relative">
                  <input
                    type="password"
                    v-model="form.password"
                    id="password"
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  >
                  <button
                    type="button"
                    @click="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                    aria-label="Mostrar contraseña"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                <p class="text-xs text-slate-400 mt-1">
                  Debe tener al menos 8 caracteres y ser diferente de la contraseña actual.
                </p>
              </div>

              <!-- CONFIRMAR CONTRASEÑA -->
              <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                  Confirmar nueva contraseña
                </label>
                <div class="relative">
                  <input
                    type="password"
                    v-model="form.password_confirmation"
                    id="password_confirmation"
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  >
                  <button
                    type="button"
                    @click="togglePassword('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-700"
                    aria-label="Mostrar contraseña"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- OLVIDE MI CONTRASEÑA -->
              <div class="pt-2">
                <a
                  :href="routes.passwordRequest"
                  class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline"
                >
                  ¿Olvidaste tu contraseña?
                </a>
              </div>
            </div>
          </div>

          <!-- BOTON -->
          <div class="flex justify-end pt-4">
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-[#044e39] hover:bg-[#033b2b] text-white font-semibold px-6 py-3 rounded-lg shadow transition disabled:opacity-50"
            >
              <span v-if="form.processing">Guardando...</span>
              <span v-else>Guardar cambios</span>
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  usuario: { type: Object, required: true },
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  email: props.usuario.email || '',
  telefono: props.usuario.telefono || '',
  password_actual: '',
  password: '',
  password_confirmation: ''
})

const togglePassword = (inputId) => {
  const input = document.getElementById(inputId)
  if (input) {
    input.type = input.type === 'password' ? 'text' : 'password'
  }
}

const submitForm = () => {
  form.put(props.routes.cuentaUpdate || '/admin/cuenta')
}
</script>
