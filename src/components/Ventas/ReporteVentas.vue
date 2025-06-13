<template>
  <section class="min-h-screen bg-[#FFF8F1] p-6">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
      <div class="flex items-center mb-4 md:mb-0">
        <i class="fas fa-cash-register text-4xl mr-3 text-[#FF6F00]"></i>
        <h1 class="text-3xl font-bold text-[#4E342E]">Reporte de ventas</h1>
      </div>
      <div>
        <span class="inline-block bg-orange-600 text-white text-3xl font-bold rounded px-6 py-2 shadow">
          Total ${{ totalVentas }}
        </span>
      </div>
    </div>

    <!-- Filtros superiores -->
    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
      <button
        class="flex items-center px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-[#4E342E] font-semibold transition"
        @click="recargar">
        <i class="fas fa-sync-alt mr-2"></i> Recargar
      </button>
      <button
        class="flex items-center px-4 py-2 rounded bg-[#FF6F00] hover:bg-[#E65100] text-white font-semibold transition"
        @click="filtrar = !filtrar">
        <i class="fas fa-filter mr-2"></i> Filtrar
      </button>
      <select v-model="perPage"
        class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]">
        <option value="5">5 por página</option>
        <option value="10">10 por página</option>
        <option value="15">15 por página</option>
        <option value="20">20 por página</option>
      </select>
    </div>

    <!-- Filtros avanzados -->
    <div v-if="filtrar" class="bg-white rounded-xl shadow p-4 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <label class="block mb-1 font-semibold text-[#4E342E]">Periodo de tiempo</label>
          <div class="flex gap-2">
            <input type="date" v-model="fechaInicio" @change="buscarEnFecha"
              class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]" />
            <span class="self-center text-[#4E342E]">a</span>
            <input type="date" v-model="fechaFin" @change="buscarEnFecha"
              class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]" />
          </div>
        </div>
        <div class="flex-1">
          <label class="block mb-1 font-semibold text-[#4E342E]">Ventas por usuario</label>
          <select v-model="usuarioSeleccionado" @change="buscarEnFecha"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]">
            <option value="" selected disabled>Selecciona</option>
            <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
              {{ usuario.nombre }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Totales por usuario -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
      <div class="flex items-center mb-4">
        <i class="fas fa-user text-2xl mr-2 text-[#FF6F00]"></i>
        <h2 class="text-xl font-bold text-[#4E342E]">Totales de ventas por usuario</h2>
      </div>
      <div class="flex flex-wrap gap-2">
        <div v-for="usuario in ventasPorUsuario" :key="usuario.nombre"
          class="flex items-center bg-[#FFF8F1] rounded px-3 py-2 shadow">
          <span class="font-semibold text-[#4E342E] mr-2">{{ usuario.nombre }}</span>
          <span class="font-bold text-[#FF6F00]">${{ usuario.total }}</span>
        </div>
      </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">#</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Mesa</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Fecha</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Atendió</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Cliente</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Pago</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Cambio</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Total</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Insumos</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="venta in ventasPaginadas" :key="venta.id" class="hover:bg-[#FFF8F1]">
            <td class="px-4 py-2">{{ venta.id }}</td>
            <td class="px-4 py-2">Mesa #{{ venta.idMesa }}</td>
            <td class="px-4 py-2">{{ venta.fecha }}</td>
            <td class="px-4 py-2">{{ venta.atendio }}</td>
            <td class="px-4 py-2">{{ venta.cliente }}</td>
            <td class="px-4 py-2">${{ venta.pagado }}</td>
            <td class="px-4 py-2">${{ venta.pagado - venta.total }}</td>
            <td class="px-4 py-2 font-bold text-[#FF6F00]">${{ venta.total }}</td>
            <td class="px-4 py-2">
              <table class="min-w-full text-xs">
                <thead>
                  <tr>
                    <th class="px-2 py-1 text-left font-semibold text-[#4E342E]">Código</th>
                    <th class="px-2 py-1 text-left font-semibold text-[#4E342E]">Nombre</th>
                    <th class="px-2 py-1 text-left font-semibold text-[#4E342E]">Cantidad</th>
                    <th class="px-2 py-1 text-left font-semibold text-[#4E342E]">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="insumo in venta.insumos" :key="insumo.codigo">
                    <td class="px-2 py-1">{{ insumo.codigo }}</td>
                    <td class="px-2 py-1">{{ insumo.nombre }}</td>
                    <td class="px-2 py-1">{{ insumo.cantidad }} x ${{ insumo.precio }}</td>
                    <td class="px-2 py-1">${{ insumo.cantidad * insumo.precio }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
            <td class="px-4 py-2">
              <button
                class="flex items-center px-3 py-1 rounded bg-blue-600 hover:bg-blue-800 text-white font-semibold transition"
                @click="imprimirComprobante(venta)" title="Imprimir ticket">
                <i class="fas fa-print"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Paginación -->
      <div class="flex justify-end mt-4 gap-2">
        <button class="px-3 py-1 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold"
          :disabled="currentPage === 1" @click="currentPage--">
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="text-[#4E342E] font-semibold px-2">Página {{ currentPage }}</span>
        <button class="px-3 py-1 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold"
          :disabled="currentPage === totalPages" @click="currentPage++">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Ticket y loading -->
    <ticket @impreso="onImpreso" :venta="this.ventaSeleccionada" :insumos="insumosSeleccionados" :datosLocal="datos"
      :logo="logo" v-if="mostrarTicket" />
    <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
  </section>
</template>

<script>
import HttpService from "../../Servicios/HttpService";
import Utiles from "../../Servicios/Utiles";
import Ticket from "./Ticket.vue";

export default {
  name: "ReporteVentas",
  components: { Ticket },

  data: () => ({
    usuarios: [],
    filtrar: false,
    datos: {},
    ventaSeleccionada: {},
    insumosSeleccionados: [],
    fechaInicio: "",
    fechaFin: "",
    usuarioSeleccionado: "",
    filtros: {},
    cargando: false,
    ventas: [],
    ventasPorUsuario: [],
    totalVentas: 0,
    mostrarTicket: false,
    currentPage: 1,
    perPage: 20,
    logo: null,
  }),

  computed: {
    totalPages() {
      return Math.ceil(this.ventas.length / this.perPage) || 1;
    },
    ventasPaginadas() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.ventas.slice(start, start + parseInt(this.perPage));
    }
  },

  mounted() {
    this.obtenerVentas();
    this.obtenerDatos();
  },

  methods: {
    recargar() {
      this.fechaInicio = "";
      this.fechaFin = "";
      this.usuarioSeleccionado = "";
      this.filtros = {};
      this.obtenerVentas();
    },

    onImpreso(resultado) {
      this.mostrarTicket = resultado;
    },

    imprimirComprobante(venta) {
      this.ventaSeleccionada = {
        atendio: venta.atendio,
        cliente: venta.cliente,
        fecha: venta.fecha,
        pagado: venta.pagado,
        total: venta.total,
      };

      this.insumosSeleccionados = venta.insumos;
      this.mostrarTicket = true;
    },

    buscarEnFecha() {
      this.filtros = {};
      if (this.fechaInicio && this.fechaFin) {
        this.filtros.inicio = this.fechaInicio;
        this.filtros.fin = this.fechaFin;
      }
      if (this.usuarioSeleccionado) {
        this.filtros.idUsuario = this.usuarioSeleccionado;
      }
      this.obtenerVentas();
    },

    obtenerVentas() {
      this.cargando = true;
      HttpService.obtenerConDatos(this.filtros, "obtener_ventas.php").then(
        (resultado) => {
          this.ventas = resultado.ventas;
          this.ventasPorUsuario = resultado.ventasPorUsuario;
          this.usuarios = resultado.usuarios;

          let total = 0;
          this.ventas.forEach((venta) => {
            total = parseFloat(total) + parseFloat(venta.total);
          });

          this.totalVentas = total;
          this.cargando = false;
        }
      );
    },

    obtenerDatos() {
      HttpService.obtener("obtener_datos_local.php").then((resultado) => {
        this.datos = resultado;
        this.logo = Utiles.generarUrlImagen(this.datos.logo);
      });
    },
  },
};
</script>