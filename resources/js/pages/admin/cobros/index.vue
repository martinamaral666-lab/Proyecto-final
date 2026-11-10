<template>
  <div class="cobro-layout">
    <!-- Header Navigation -->
    <nav class="navbar">
      <div class="navbar-brand">
        <Link href="/menu/admin" class="px-4 py-2 bg-[#033827] hover:bg-[#02261b] text-emerald-200 text-sm font-semibold rounded-lg transition-all">
          &larr; Volver
        </Link>

        <img
          src="/imagenes/logo.png"
          alt="Logo GestiónCash"
          class="navbar-logo"
        >

        <span class="brand-title">
          GestiónCash
        </span>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
      <div class="section-header">
        <h2>
          Historial de Cobros
        </h2>
      </div>

      <!-- BUSCADOR -->
      <form @submit.prevent="buscar" class="search-bar">
        <div class="search-field">
          <span class="search-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input type="text" v-model="searchQuery" placeholder="Buscar cliente, teléfono o concepto..." class="search-input">
        </div>
        <button type="submit" class="btn-search">Buscar</button>
        <button v-if="filters.search" type="button" class="btn-clear" @click="limpiar">Limpiar</button>
      </form>

      <div class="card-table">
        <!-- Si hay cobros -->
        <template v-if="cobros.data && cobros.data.length > 0">
          <div v-for="cobro in cobros.data" :key="cobro.id" class="cobro-item">
            <div class="cobro-info">
              <div>
                <span class="cliente-name">
                  {{ cobro.nombre_cliente }}
                </span>
                <span class="cliente-phone">
                  ({{ cobro.telefono }})
                </span>
              </div>

              <!-- TIPO DE REGISTRO -->
              <span class="badge badge-type">
                {{ cobro.tipo_registro === 'venta' ? 'Venta en caja' : 'Mano de obra' }}
              </span>

              <span class="concepto-text">
                Concepto: {{ cobro.concepto }}
              </span>

              <!-- INFORMACIÓN DE MANO DE OBRA -->
              <template v-if="cobro.tipo_registro === 'mano_obra'">
                <span v-if="cobro.mano_de_obra === 'si'" class="badge badge-success">
                  Mano de obra: Realizada
                </span>
                <span v-else class="badge badge-danger">
                  Mano de obra: No realizada ({{ cobro.motivo_no_realizado || 'Sin motivo' }})
                </span>
              </template>

              <span class="date-text">
                {{ formatFecha(cobro.created_at) }} hs
              </span>
            </div>

            <div class="cobro-actions">
              <span class="monto-text">
                ${{ Number(cobro.monto).toFixed(2) }}
              </span>

              <!-- La ruta del PDF es /cobros/{id}/pdf (definida en web.php) -->
              <a
                :href="`/cobros/${cobro.id}/pdf`"
                target="_blank"
                class="btn-pdf"
              >
                Ver PDF
              </a>
            </div>
          </div>
        </template>

        <!-- Estado Vacío -->
        <div v-else class="empty-state">
          {{ filters.search ? 'No se encontraron cobros para esa búsqueda.' : 'No hay registros de cobros disponibles.' }}
        </div>
      </div>

      <!-- Paginador de Laravel (si viene como paginador de Inertia/Laravel) -->
      <div v-if="cobros.links && cobros.links.length > 3" class="pagination-wrapper">
        <div class="flex gap-1">
          <template v-for="(link, index) in cobros.links" :key="index">
            <component
              :is="link.url ? Link : 'span'"
              :href="link.url"
              v-html="link.label"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
              :class="[
                link.active ? 'bg-[#064e3b] text-white border-[#064e3b]' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50',
                !link.url ? 'opacity-40 cursor-not-allowed' : ''
              ]"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  cobros: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({ search: '' }) },
})

const searchQuery = ref(props.filters?.search ?? '')

const buscar = () => {
  router.get('/cobros', { search: searchQuery.value || undefined }, { preserveState: true })
}

const limpiar = () => {
  searchQuery.value = ''
  buscar()
}

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
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: system-ui, -apple-system, sans-serif;
}

.cobro-layout {
    background: #f1f5f9;
    color: #1e293b;
    min-height: 100vh;
}

/* Navbar */
.navbar {
    background: #064e3b;
    color: #fff;
    padding: 15px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.navbar-logo {
    width: 38px;
    height: 38px;
    object-fit: contain;
    border-radius: 10px;
    background: rgba(255,255,255,0.08);
    padding: 4px;
}

.btn-back {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.25);
}

.brand-title {
    font-size: 1.25rem;
    font-weight: 700;
}

/* Contenedor Principal */
.container {
    max-width: 900px;
    margin: 30px auto;
    padding: 0 20px;
}

/* Header Sección */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #0f172a;
    font-weight: 700;
}

/* Tarjeta Contenedora */
.card-table {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

/* Lista de Cobros */
.cobro-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}

.cobro-item:last-child {
    border-bottom: none;
}

.cobro-item:hover {
    background: #f8fafc;
}

/* Info Izquierda */
.cobro-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.cliente-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 1rem;
}

.cliente-phone {
    font-weight: 400;
    color: #64748b;
    font-size: 0.85rem;
    margin-left: 6px;
}

.concepto-text {
    color: #475569;
    font-size: 0.9rem;
}

.date-text {
    color: #94a3b8;
    font-size: 0.78rem;
    margin-top: 2px;
}

/* Estado Badge */
.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 4px;
    width: fit-content;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-type {
    background: #e0f2fe;
    color: #075985;
}

/* Info Derecha */
.cobro-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.monto-text {
    font-size: 1.15rem;
    font-weight: 800;
    color: #064e3b;
    text-align: right;
}

.btn-pdf {
    background: #f1f5f9;
    color: #334155;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #cbd5e0;
    transition: all 0.2s;
}

.btn-pdf:hover {
    background: #064e3b;
    color: #fff;
    border-color: #064e3b;
}

/* Estado Vacío */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
    font-size: 0.95rem;
}

/* Paginación */
.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

/* Buscador */
.search-bar {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.search-field {
    position: relative;
    flex: 1;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    display: flex;
}

.search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.9rem;
    outline: none;
}

.search-input:focus {
    border-color: #044e39;
}

.btn-search {
    background: #1e293b;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-search:hover {
    background: #0f172a;
}

.btn-clear {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-clear:hover {
    background: #e2e8f0;
}
</style>
