<template>
  <div class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">
      <!-- HEADER -->
      <div class="bg-emerald-800 text-white p-6 text-center">
        <img src="/imagenes/logo.png" alt="Logo GestiónCash" class="w-16 h-16 object-contain mx-auto mb-3 rounded-xl bg-white/10 p-2">
        <h1 class="text-xl font-bold">Registrar Cobro</h1>
        <p class="text-xs text-emerald-100 mt-1">Ingresa los datos del pago recibido</p>
      </div>

      <!-- FORMULARIO -->
      <form @submit.prevent="submit" class="p-6 space-y-4">
        <!-- ERRORES -->
        <div v-if="Object.keys(form.errors).length > 0" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
          <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
        </div>

        <!-- NOMBRE CLIENTE -->
        <div>
          <label for="nombre_cliente" class="block text-xs font-bold uppercase text-gray-600 mb-1">Nombre del Cliente</label>
          <input
            type="text"
            v-model="form.nombre_cliente"
            id="nombre_cliente"
            required
            placeholder="Ej. Juan Pérez"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
          >
        </div>

        <!-- TELÉFONO -->
        <div>
          <label for="telefono" class="block text-xs font-bold uppercase text-gray-600 mb-1">Teléfono (WhatsApp)</label>
          <input
            type="tel"
            v-model="form.telefono"
            id="telefono"
            required
            placeholder="Ej. 099123456"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
          >
        </div>

        <!-- CONCEPTO -->
        <div>
          <label for="concepto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Concepto</label>
          <input
            type="text"
            v-model="form.concepto"
            id="concepto"
            required
            placeholder="Ej. Cuota de servicio"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
          >
        </div>

        <!-- MONTO -->
        <div>
          <label for="monto" class="block text-xs font-bold uppercase text-gray-600 mb-1">Monto ($)</label>
          <input
            type="number"
            step="0.01"
            v-model="form.monto"
            id="monto"
            required
            placeholder="0.00"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
          >
        </div>

        <!-- MEDIO DE PAGO -->
        <div>
          <label for="metodo_pago" class="block text-xs font-bold uppercase text-gray-600 mb-1">
            Medio de pago
          </label>
          <select
            v-model="form.metodo_pago"
            id="metodo_pago"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
          >
            <option value="" disabled>Seleccioná un medio de pago</option>
            <option value="efectivo">💵 Efectivo</option>
            <option value="tarjeta">💳 Tarjeta</option>
          </select>
        </div>

        <!-- ¿SE REALIZÓ LA MANO DE OBRA? -->
        <div>
          <label for="mano_de_obra" class="block text-xs font-bold uppercase text-gray-600 mb-1">
            ¿Se realizó la mano de obra?
          </label>
          <select
            v-model="form.mano_de_obra"
            id="mano_de_obra"
            @change="toggleMotivo"
            required
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-emerald-600"
          >
            <option value="si">Sí, realizada</option>
            <option value="no">No realizada</option>
          </select>
        </div>

        <!-- MONTO MANUAL -->
        <div v-show="form.mano_de_obra === 'si'" id="grupo-monto-manual">
          <label for="monto_base" class="mb-1 block text-xs font-bold uppercase text-gray-600">
            Monto manual (mano de obra/otros) ($)
          </label>
          <input
            type="number"
            step="1"
            min="0"
            v-model="form.monto_base"
            id="monto_base"
            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-800 focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
          >
        </div>

        <!-- MOTIVO POR EL CUAL NO SE REALIZÓ -->
        <div v-show="form.mano_de_obra === 'no'" id="campo_motivo">
          <label for="motivo_no_realizado" class="block text-xs font-bold uppercase text-red-600 mb-1">
            Motivo por el cual no se realizó
          </label>
          <textarea
            v-model="form.motivo_no_realizado"
            id="motivo_no_realizado"
            rows="2"
            placeholder="Explica la razón..."
            class="w-full bg-red-50 border border-red-200 rounded-xl px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:border-red-500"
          ></textarea>
        </div>

        <!-- BOTONES -->
        <div class="pt-2 space-y-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-emerald-800 hover:bg-emerald-900 text-white font-bold py-3 rounded-xl transition shadow-md disabled:opacity-50 cursor-pointer"
          >
            Guardar y Generar Recibo
          </button>

          <Link
            href="/admin/menu"
            class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl text-sm transition"
          >
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  routes: { type: Object, default: () => ({}) }
})

const form = useForm({
  nombre_cliente: '',
  telefono: '',
  concepto: '',
  monto: '',
  metodo_pago: '',
  mano_de_obra: 'si',
  monto_base: '0.00',
  motivo_no_realizado: ''
})

const toggleMotivo = () => {
  if (form.mano_de_obra === 'no') {
    form.monto_base = '0.00'
  } else {
    form.motivo_no_realizado = ''
  }
}

const submit = () => {
  form.post(props.routes.cobrosStore || '/admin/cobros')
}
</script>
