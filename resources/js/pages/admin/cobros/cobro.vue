<template>
  <div class="cobro-body">
    <!-- CERRAR SESIÓN -->
    <form @submit.prevent="logout" class="logout-form">
      <button
        type="submit"
        class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow transition-all cursor-pointer"
      >
        Cerrar Sesión
      </button>
    </form>

    <!-- TARJETA PRINCIPAL -->
    <div class="card-cobro">
      <!-- ENCABEZADO -->
      <div class="card-header">
        <img src="/imagenes/logo.png" alt="Logo GestiónCash">
        <h2>Registrar Cobro</h2>
      </div>

      <!-- ERRORES -->
      <div v-if="Object.keys(form.errors).length > 0" class="alert-error">
        <ul>
          <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <form @submit.prevent="submitForm">
        <!-- TIPO DE REGISTRO -->
        <div class="form-group">
          <label>¿Qué deseas registrar?</label>
          <div class="tipo-registro">
            <div class="tipo-opcion">
              <input
                type="radio"
                id="venta_caja"
                value="venta"
                v-model="form.tipo_registro"
                @change="actualizarTipoRegistro"
              >
              <label for="venta_caja">Venta en caja</label>
            </div>

            <div class="tipo-opcion">
              <input
                type="radio"
                id="mano_obra"
                value="mano_obra"
                v-model="form.tipo_registro"
                @change="actualizarTipoRegistro"
              >
              <label for="mano_obra">Mano de obra</label>
            </div>
          </div>
        </div>

        <!-- DATOS DEL CLIENTE -->
        <div class="seccion-titulo">Datos del cliente</div>

        <div class="form-group">
          <label for="nombre_cliente">Nombre del Cliente</label>
          <input
            type="text"
            id="nombre_cliente"
            v-model="form.nombre_cliente"
            class="form-control"
            required
          >
        </div>

        <div class="form-group">
          <label for="telefono">Teléfono (WhatsApp)</label>
          <input
            type="text"
            id="telefono"
            v-model="form.telefono"
            class="form-control"
            placeholder="099123456"
            required
          >
        </div>

        <!-- CONCEPTO -->
        <div class="form-group">
          <label for="concepto">Concepto / Descripción</label>
          <input
            type="text"
            id="concepto"
            v-model="form.concepto"
            class="form-control"
            placeholder="Ej: Materiales eléctricos"
            required
          >
        </div>

        <!-- SECCIÓN MANO DE OBRA -->
        <div :class="{ oculto: form.tipo_registro !== 'mano_obra' }" id="seccion-mano-obra">
          <div class="seccion-titulo">Datos de la mano de obra</div>

          <div class="form-group">
            <label for="monto_base">Monto manual (mano de obra/otros) ($)</label>
            <input
              type="number"
              id="monto_base"
              v-model="form.monto_base"
              class="form-control"
              step="0.01"
              min="0"
            >
          </div>

          <div class="form-group">
            <label for="mano_de_obra">¿Se realizó la mano de obra?</label>
            <select
              id="mano_de_obra"
              v-model="form.mano_de_obra"
              class="form-control"
              @change="actualizarMotivo"
            >
              <option value="si">Sí</option>
              <option value="no">No</option>
            </select>
          </div>

          <div :class="{ oculto: !(form.tipo_registro === 'mano_obra' && form.mano_de_obra === 'no') }" class="form-group" id="grupo-motivo">
            <label for="motivo_no_realizado">Motivo por el que no se realizó</label>
            <input
              type="text"
              id="motivo_no_realizado"
              v-model="form.motivo_no_realizado"
              class="form-control"
              placeholder="Ingresá el motivo"
              :required="form.tipo_registro === 'mano_obra' && form.mano_de_obra === 'no'"
            >
          </div>
        </div>


        <div v-if="form.tipo_registro === 'venta'" class="form-group">
          <label for="monto_manual">
            Monto ($)
            <small>(mano de obra, servicio u otros)</small>
          </label>
          <input
            type="number"
            id="monto_manual"
            v-model="form.monto_base"
            class="form-control"
            step="0.01"
            min="0"
            placeholder="0.00"
            :required="form.productos.length === 0"
          >
        </div>

        <!-- PRODUCTOS DEL INVENTARIO + MONTO TOTAL -->
        <div class="form-group">
          <CobrosDeInventario
            v-model="form.productos"
            v-model:monto="form.monto"
            :items-inventario="itemsInventario"
            :monto-base="montoBase"
          />
        </div>

        <!-- MEDIO DE PAGO -->
        <div class="form-group">
          <label for="metodo_pago">Medio de pago</label>
          <select
            id="metodo_pago"
            v-model="form.metodo_pago"
            class="form-control"
            required
          >
            <option value="" disabled>Seleccioná un medio de pago</option>
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
          </select>
        </div>

        <!-- BOTÓN GUARDAR -->
        <button
          id="receipt-submit-button"
          type="submit"
          class="btn-submit"
          :disabled="form.processing || remainingSeconds > 0"
        >
          <span v-if="remainingSeconds > 0">Espera {{ remainingSeconds }}s</span>
          <span v-else-if="form.processing">Generando recibo...</span>
          <span v-else>Guardar y Generar Recibo</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import CobrosDeInventario from './cobrosdeinventario.vue'

const props = defineProps({
  receiptWaitSeconds: { type: Number, default: 0 },
  itemsInventario: { type: Array, default: () => [] },
})

const form = useForm({
  tipo_registro: 'venta',
  nombre_cliente: '',
  telefono: '',
  concepto: '',
  monto: '0.00',          // lo calcula el selector de productos (base + productos)
  metodo_pago: '',
  monto_base: '',         // monto manual: mano de obra, servicio u otros
  mano_de_obra: 'si',
  motivo_no_realizado: '',
  productos: []           // [{ inventario_id, cantidad }]
})

/*
 * Monto "base" que el selector suma a los productos. Replica la regla del servidor,
 * para que el total que se ve en pantalla sea el mismo que se guarda:
 *  - Venta en caja: el monto escrito (si hay) + los productos.
 *  - Mano de obra realizada: el monto de mano de obra + los productos.
 *  - Mano de obra no realizada: solo los productos.
 */
const montoBase = computed(() => {
  const escrito = Number(form.monto_base) || 0

  if (form.tipo_registro === 'mano_obra') {
    return form.mano_de_obra === 'si' ? escrito : 0
  }

  return escrito
})

const remainingSeconds = ref(props.receiptWaitSeconds)
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

const actualizarTipoRegistro = () => {
  form.monto_base = ''

  if (form.tipo_registro !== 'mano_obra') {
    form.motivo_no_realizado = ''
  }
}

const actualizarMotivo = () => {
  if (form.mano_de_obra === 'si') {
    form.motivo_no_realizado = ''
  }
}

const submitForm = () => {
  form.post('/cobros')
}

const logout = () => {
  router.post('/logout')
}
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}

.cobro-body {
    background: #009966;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    position: relative;
    width: 100vw;
    top: 0;
    left: 0;
}

.logout-form {
    position: absolute;
    top: 20px;
    right: 20px;
}

.card-cobro {
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
}

.card-header {
    text-align: center;
    margin-bottom: 20px;
}

.card-header img {
    display: block;
    width: 84px;
    height: 84px;
    object-fit: contain;
    margin: 0 auto 12px;
    background: #ecfdf5;
    border-radius: 18px;
    padding: 10px;
}

.card-header h2 {
    color: #0f172a;
    font-size: 1.4rem;
    margin-bottom: 4px;
}

.form-group {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 5px;
    color: #334155;
}

.form-group label small {
    font-weight: normal;
    color: #94a3b8;
}

.form-control {
    width: 100%;
    padding: 10px;
    background: #f8fafc;
    border: 1px solid #cbd5e0;
    border-radius: 8px;
    outline: none;
    font-size: 0.95rem;
}

.btn-submit {
    width: 100%;
    padding: 12px;
    background: #009966;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.2s;
}

.btn-submit:hover {
    background: #058257;
}

.btn-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.85rem;
}

.seccion-titulo {
    font-size: 0.9rem;
    font-weight: bold;
    color: #009966;
    margin: 20px 0 10px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tipo-registro {
    display: flex;
    gap: 15px;
    margin-top: 5px;
}

.tipo-opcion {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e0;
    padding: 8px 12px;
    border-radius: 8px;
    flex: 1;
    cursor: pointer;
}

.tipo-opcion label {
    margin-bottom: 0;
    cursor: pointer;
    font-weight: normal;
}

.oculto {
    display: none !important;
}
</style>
