<template>
  <div class="login-body">
    <div class="login-container">
      <div class="login-card">

        <!-- LOGO -->
        <div class="logo">
          <img src="/imagenes/logo.png" alt="Logo">
          <h1>Iniciar sesión</h1>
        </div>

        <!-- ERROR -->
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <!-- ÉXITO -->
        <div v-if="$page.props.flash?.success" class="success-message">
          {{ $page.props.flash.success }}
        </div>

        <!-- FORMULARIO -->
        <form @submit.prevent="submitForm">
          <!-- CORREO -->
          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              autofocus
              placeholder="tu@correo.com"
            >
          </div>

          <!-- CONTRASEÑA -->
          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-wrapper">
              <input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                required
                placeholder="••••••••"
              >

              <!-- OJO -->
              <button
                type="button"
                @click="togglePassword"
                class="toggle-password"
                :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                :title="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
              >
                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <template v-if="!showPassword">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7z"/>
                  </template>
                  <template v-else>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.18 6.18A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.042 5.05"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                  </template>
                </svg>
              </button>
            </div>
          </div>

          <!-- INGRESAR -->
          <button type="submit" class="btn-submit" :disabled="form.processing || remainingSeconds > 0">
            <span v-if="remainingSeconds > 0">Espera {{ remainingSeconds }}s</span>
            <span v-else-if="form.processing">Ingresando...</span>
            <span v-else>Ingresar</span>
          </button>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
  loginWaitSeconds: { type: Number, default: 0 },
  routes: { type: Object, default: () => ({}) }
})

const page = usePage()

const form = useForm({
  email: '',
  password: ''
})

const showPassword = ref(false)
const remainingSeconds = ref(props.loginWaitSeconds)
let countdown = null

const errorMessage = computed(() => {
  const errors = page.props.errors
  if (errors && Object.keys(errors).length > 0) {
    return Object.values(errors)[0]
  }
  return null
})

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

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const submitForm = () => {
  form.post(props.routes.loginPost || '/login', {
    onError: () => {
      if (page.props.flash?.login_wait_seconds) {
        remainingSeconds.value = Number(page.props.flash.login_wait_seconds)
        if (remainingSeconds.value > 0 && !countdown) {
          countdown = setInterval(() => {
            if (remainingSeconds.value <= 0) {
              clearInterval(countdown)
            } else {
              remainingSeconds.value -= 1
            }
          }, 1000)
        }
      }
    }
  })
}
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.login-body {
    font-family: Arial, Helvetica, sans-serif;
    background: #009966;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    width: 100vw;
    position: fixed;
    top: 0;
    left: 0;
}

.login-container {
    width: 100%;
    max-width: 420px;
}

.login-card {
    background: white;
    border-radius: 18px;
    padding: 40px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.logo {
    text-align: center;
    margin-bottom: 30px;
}

.logo img {
    display: block;
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin: 0 auto 15px auto;
}

.logo h1 {
    color: #044e39;
    font-size: 30px;
    font-weight: bold;
    margin: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-size: 14px;
    font-weight: 600;
}

.form-group input {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
    transition: 0.2s;
}

.form-group input:focus {
    border-color: #009966;
    box-shadow: 0 0 0 2px rgba(0, 153, 102, 0.15);
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 48px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.toggle-password:hover {
    color: #047857;
}

.eye-icon {
    width: 20px;
    height: 20px;
}

.btn-submit {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #044e39;
    color: white;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-submit:hover {
    background: #033b2b;
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-submit:disabled {
    background-color: #64748b;
    cursor: not-allowed;
    transform: none;
}

.error-message {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.success-message {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}
</style>
