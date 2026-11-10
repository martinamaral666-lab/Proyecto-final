<template>
  <section class="space-y-3">
    <!-- Botón para desplegar / ocultar panel -->
    <button
      type="button"
      @click="togglePanel"
      :aria-expanded="isExpanded"
      aria-controls="inventory-products-panel"
      class="w-full rounded-xl border border-emerald-700 px-3 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50 cursor-pointer"
    >
      {{ isExpanded ? 'Ocultar productos del inventario' : 'Agregar productos del inventario' }}
    </button>

    <!-- Panel de productos -->
    <div v-show="isExpanded" id="inventory-products-panel" class="space-y-3 rounded-xl border border-gray-200 bg-gray-50 p-3">
      <div class="space-y-3">
        <div
          v-for="(row, index) in productos"
          :key="index"
          class="grid grid-cols-1 gap-2 sm:grid-cols-[minmax(0,1fr)_7rem_auto] sm:items-end"
        >
          <!-- Selector de Producto -->
          <div>
            <label :for="`inventory-product-${index}`" class="mb-1 block text-xs font-semibold text-gray-600">Producto</label>
            <select
              :id="`inventory-product-${index}`"
              v-model="row.inventario_id"
              @change="clampRow(row)"
              class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800"
            >
              <option value="" disabled>Selecciona un producto</option>
              <option
                v-for="item in itemsInventario"
                :key="item.id"
                :value="item.id"
                :disabled="isOptionDisabled(item, row.inventario_id)"
              >
                {{ item.nombre_item }} — ${{ Number(item.precio).toFixed(2) }} (stock: {{ item.stock_actual }})
              </option>
            </select>
          </div>

          <!-- Cantidad -->
          <div>
            <label :for="`inventory-quantity-${index}`" class="mb-1 block text-xs font-semibold text-gray-600">Cantidad</label>
            <input
              type="number"
              :id="`inventory-quantity-${index}`"
              v-model.number="row.cantidad"
              @change="clampRow(row)"
              min="1"
              step="1"
              :max="getMaxStock(row.inventario_id)"
              class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800"
            >
          </div>

          <!-- Botón Quitar -->
          <button
            type="button"
            @click="removeRow(index)"
            class="rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 cursor-pointer"
          >
            Quitar
          </button>
        </div>
      </div>

      <!-- Botón Añadir otro producto o mensaje de stock agotado -->
      <button
        v-if="hasAvailableStock"
        type="button"
        @click="addRow()"
        class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
      >
        Añadir otro producto
      </button>
      <p v-else class="text-sm text-gray-600">No hay productos con stock disponible.</p>

      <!-- Alerta de stock insuficiente -->
      <p v-if="hasInsufficientStock" role="alert" aria-live="assertive" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
        No hay suficiente stock.
      </p>
    </div>

    <!-- Input Monto Total -->
    <div>
      <label for="monto" class="mb-1 block text-xs font-bold uppercase text-gray-600">Monto total ($)</label>
      <input
        type="number"
        step="0.01"
        min="0"
        id="monto"
        :value="montoTotal"
        readonly
        class="w-full rounded-xl border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm font-bold text-gray-800"
      >
    </div>
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

/*
 * Uso en el formulario de cobros:
 *
 *   <CobrosDeInventario
 *     v-model="form.productos"
 *     v-model:monto="form.monto"
 *     :items-inventario="itemsInventario"
 *     :monto-base="manoDeObraEscrita"
 *   />
 *
 * - v-model entrega solo filas completas: [{ inventario_id, cantidad }].
 * - monto-base es el costo de la mano de obra (0 si es una venta en caja).
 * - v-model:monto recibe base + productos, ya calculado.
 */

const props = defineProps({
  itemsInventario: { type: Array, default: () => [] },
  modelValue: { type: Array, default: () => [] }, // Sincroniza con form.productos
  montoBase: { type: [Number, String], default: 0 }
})

const emit = defineEmits(['update:modelValue', 'update:monto'])

const isExpanded = ref(false)
const productos = ref([...props.modelValue])

const findItem = (id) => props.itemsInventario.find(i => String(i.id) === String(id))

// Al formulario solo se envían las filas completas. Una fila a medio llenar
// (sin producto elegido) hacía fallar la validación del servidor.
watch(productos, (newVal) => {
  emit(
    'update:modelValue',
    newVal
      .filter(row => row.inventario_id && Number(row.cantidad) >= 1)
      .map(row => ({
        inventario_id: Number(row.inventario_id),
        cantidad: Math.floor(Number(row.cantidad))
      }))
  )
}, { deep: true })

const togglePanel = () => {
  isExpanded.value = !isExpanded.value
  if (isExpanded.value && productos.value.length === 0 && props.itemsInventario.length > 0) {
    addRow()
  }
}

const addRow = (product = { inventario_id: '', cantidad: 1 }) => {
  productos.value.push({
    inventario_id: product.inventario_id ?? '',
    cantidad: product.cantidad ?? 1
  })
}

const removeRow = (index) => {
  productos.value.splice(index, 1)
}

// Cantidades totales solicitadas por cada ID de inventario
const requestedQuantities = computed(() => {
  const map = new Map()
  productos.value.forEach(row => {
    if (row.inventario_id) {
      const qty = Number(row.cantidad) || 0
      map.set(String(row.inventario_id), (map.get(String(row.inventario_id)) || 0) + qty)
    }
  })
  return map
})

const hasAvailableStock = computed(() => {
  return props.itemsInventario.some(item => {
    const requested = requestedQuantities.value.get(String(item.id)) || 0
    return Number(item.stock_actual) > requested
  })
})

const isOptionDisabled = (item, currentSelectedId) => {
  if (Number(item.stock_actual) <= 0) return true
  const totalRequested = requestedQuantities.value.get(String(item.id)) || 0
  const currentSelectionQty = productos.value.find(r => r.inventario_id == item.id)?.cantidad || 0
  const stockAvailableWithoutThisRow = Number(item.stock_actual) - (totalRequested - currentSelectionQty)
  return String(item.id) !== String(currentSelectedId) && stockAvailableWithoutThisRow <= 0
}

const getMaxStock = (itemId) => {
  if (!itemId) return 99999
  const item = findItem(itemId)
  if (!item) return 99999
  const totalReq = requestedQuantities.value.get(String(itemId)) || 0
  const currentItemQty = productos.value.find(r => String(r.inventario_id) === String(itemId))?.cantidad || 0
  const availableForThisRow = Number(item.stock_actual) - (totalReq - currentItemQty)
  return Math.max(availableForThisRow, 0)
}

// La cantidad no puede bajar de 1 ni superar el stock disponible
const clampRow = (row) => {
  let cantidad = Math.floor(Number(row.cantidad) || 1)
  if (cantidad < 1) cantidad = 1
  if (row.inventario_id) {
    const max = getMaxStock(row.inventario_id)
    if (max >= 1 && cantidad > max) cantidad = max
  }
  row.cantidad = cantidad
}

const hasInsufficientStock = computed(() =>
  productos.value.some(row => {
    if (!row.inventario_id) return false
    const item = findItem(row.inventario_id)
    const qty = Number(row.cantidad) || 0
    return item && qty > Number(item.stock_actual)
  })
)

const productsTotalInCents = computed(() => {
  let total = 0
  productos.value.forEach(row => {
    if (row.inventario_id) {
      const item = findItem(row.inventario_id)
      const qty = Number(row.cantidad) || 0
      if (item && qty > 0) {
        total += Math.round(Number(item.precio) * 100) * qty
      }
    }
  })
  return total
})

const montoTotal = computed(() => {
  const baseCents = Math.round((Number(props.montoBase) || 0) * 100)
  const totalCents = baseCents + productsTotalInCents.value
  return (totalCents / 100).toFixed(2)
})

// Antes el total se enviaba desde los eventos @input/@change, que a veces corren
// antes de que v-model actualice el valor y dejaban el monto desfasado. Con un
// watch se actualiza siempre, también cuando cambia la mano de obra del padre.
watch(montoTotal, (valor) => {
  emit('update:monto', valor)
}, { immediate: true })

onMounted(() => {
  if (productos.value.length > 0) {
    isExpanded.value = true
  }
})
</script>
