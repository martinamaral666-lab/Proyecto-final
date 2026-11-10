<template>
  <div class="pdf-body">
    <!-- BARRA DE ACCIÓN SUPERIOR (Opcional para imprimir o volver) -->
    <div class="max-w-2xl mx-auto mb-4 flex justify-between items-center print:hidden">
      <Link :href="routes.cobrosIndex || '/admin/cobros'" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-lg transition-all">
        &larr; Volver al Listado
      </Link>
      <button
        @click="imprimirRecibo"
        class="px-4 py-2 bg-[#065f46] hover:bg-[#044e38] text-white text-sm font-semibold rounded-lg shadow transition-all cursor-pointer"
      >
         Imprimir / Guardar PDF
      </button>
    </div>

    <!-- TARJETA DEL RECIBO -->
    <div class="box max-w-2xl mx-auto bg-white shadow-md">
      <div class="header">
        <h1 class="title">
          RECIBO DE PAGO
        </h1>
        <p class="text-sm text-slate-600 mt-1">
          Fecha: {{ formatFecha(cobro.created_at) }} | N° #{{ String(cobro.id).padStart(6, '0') }}
        </p>
      </div>

      <table class="table">
        <tr>
          <th>Cliente:</th>
          <td>{{ cobro.nombre_cliente }}</td>
        </tr>
        <tr>
          <th>Teléfono:</th>
          <td>{{ cobro.telefono }}</td>
        </tr>
        <tr>
          <th>Concepto:</th>
          <td>{{ cobro.concepto }}</td>
        </tr>

        <!-- PRODUCTOS SI EXISTEN -->
        <template v-if="cobro.productos && cobro.productos.length > 0">
          <tr>
            <th colspan="2" class="text-emerald-800 bg-emerald-50/50 pt-4">Productos Asociados</th>
          </tr>
          <tr>
            <td colspan="2" class="p-0">
              <table class="table inner-table">
                <thead>
                  <tr>
                    <th>Ítem</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(producto, index) in cobro.productos" :key="index">
                    <td>{{ producto.nombre_item }}</td>
                    <td>{{ producto.cantidad }}</td>
                    <td>${{ Number(producto.precio_unitario || 0).toFixed(2) }}</td>
                    <td>${{ Number(producto.subtotal || 0).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </template>

        <tr>
          <th>Tipo de Registro:</th>
          <td>
            {{ cobro.tipo_registro === 'venta' ? 'Venta en caja' : 'Mano de obra' }}
          </td>
        </tr>

        <template v-if="cobro.tipo_registro === 'mano_obra'">
          <tr>
            <th>Mano de Obra:</th>
            <td>
              <span v-if="cobro.mano_de_obra === 'si'">Realizada</span>
              <span v-else>
                No realizada (Motivo: {{ cobro.motivo_no_realizado || 'N/A' }})
              </span>
            </td>
          </tr>
        </template>
      </table>

      <div class="total">
        Monto Total: ${{ Number(cobro.monto || 0).toFixed(2) }}
      </div>

      <div class="footer">
        <p>Gracias por su preferencia</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  cobro: { type: Object, required: true },
  routes: { type: Object, default: () => ({}) }
})

const formatFecha = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('es-UY', {
    timeZone: 'America/Montevideo',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  }).replace(',', '')
}

const imprimirRecibo = () => {
  window.print()
}
</script>

<style scoped>
.pdf-body {
    font-family: sans-serif;
    padding: 20px;
    color: #333;
    background: #f8fafc;
    min-height: 100vh;
}

.box {
    border: 2px solid #065f46;
    border-radius: 8px;
    padding: 30px;
    background: #fff;
}

.header {
    text-align: center;
    border-bottom: 2px solid #065f46;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.title {
    font-size: 22px;
    font-weight: bold;
    color: #065f46;
    margin: 0;
}

.table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

.table th {
    width: 35%;
    color: #475569;
}

.inner-table {
    margin-top: 5px;
    margin-bottom: 5px;
}

.inner-table th {
    background: #f1f5f9;
    font-size: 12px;
    text-transform: uppercase;
}

.inner-table td {
    font-size: 13px;
}

.total {
    font-size: 18px;
    font-weight: bold;
    color: #065f46;
    text-align: right;
    margin-top: 25px;
    border-top: 2px solid #e2e8f0;
    padding-top: 15px;
}

.footer {
    margin-top: 40px;
    text-align: center;
    font-size: 12px;
    color: #666;
}

@media print {
  .pdf-body {
    background: white;
    padding: 0;
  }
  .box {
    border: none;
    box-shadow: none;
    padding: 0;
  }
}
</style>
