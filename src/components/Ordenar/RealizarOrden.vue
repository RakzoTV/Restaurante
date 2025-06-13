<template>
  <section class="min-h-screen bg-[#FFF8F1] p-6">
    <div class="flex items-center mb-8">
      <i class="fas fa-list-ol text-3xl text-[#FF6F00] mr-3"></i>
      <h1 class="text-3xl font-bold text-[#4E342E]">Realizar orden</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="mesa in mesas" :key="mesa.mesa.idMesa" class="bg-white rounded-xl shadow-lg p-6 flex flex-col">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-xl font-bold text-[#4E342E]">
            <i class="fas fa-chair mr-2 text-[#FF6F00]"></i>
            Mesa #{{ mesa.mesa.idMesa }}
          </h2>
          <span v-if="mesa.mesa.total" class="text-2xl font-bold text-[#FF6F00]">
            ${{ mesa.mesa.total }}
          </span>
        </div>
        <p v-if="mesa.mesa.atiende" class="text-[#4E342E]">
          <strong>Atiende:</strong> {{ mesa.mesa.atiende }}
        </p>
        <p v-if="mesa.mesa.cliente" class="text-[#4E342E]">
          <strong>Cliente:</strong> {{ mesa.mesa.cliente }}
        </p>

        <!-- Insumos en la orden (collapse) -->
        <div v-if="mesa.mesa.estado === 'ocupada'" class="mt-4">
          <button @click="mesa.showInsumos = !mesa.showInsumos"
            class="w-full flex items-center justify-between px-4 py-2 rounded bg-[#FFF8F1] border border-[#FF6F00] text-[#4E342E] font-semibold focus:outline-none focus:ring-2 focus:ring-[#FF6F00] transition">
            <span>
              <i class="fas fa-utensils mr-2"></i>
              Insumos en la orden
            </span>
            <i :class="mesa.showInsumos ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
          </button>
          <div v-if="mesa.showInsumos" class="mt-2">
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm text-[#4E342E]">
                <thead>
                  <tr class="bg-[#FFF8F1]">
                    <th class="px-3 py-2 text-left font-semibold">Código</th>
                    <th class="px-3 py-2 text-left font-semibold">Nombre</th>
                    <th class="px-3 py-2 text-left font-semibold">Características</th>
                    <th class="px-3 py-2 text-left font-semibold">Cantidad</th>
                    <th class="px-3 py-2 text-left font-semibold">Subtotal</th>
                    <th class="px-3 py-2 text-center font-semibold">Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="insumo in mesa.insumos" :key="insumo.id" class="hover:bg-[#FFF8F1]">
                    <td class="px-3 py-2">{{ insumo.codigo }}</td>
                    <td class="px-3 py-2">{{ insumo.nombre }}</td>
                    <td class="px-3 py-2">{{ insumo.caracteristicas }}</td>
                    <td class="px-3 py-2">
                      {{ insumo.cantidad }} x ${{ insumo.precio }}
                    </td>
                    <td class="px-3 py-2">
                      ${{ insumo.cantidad * insumo.precio }}
                    </td>
                    <td class="px-3 py-2 text-center">
                      <i v-if="insumo.estado === 'pendiente'" class="fas fa-exclamation-circle text-red-600"
                        title="Pendiente"></i>
                      <i v-if="insumo.estado === 'entregado'" class="fas fa-check-circle text-green-600"
                        title="Entregado"></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col items-center mt-6 space-y-2">
          <button v-if="mesa.mesa.estado === 'libre'"
            class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
            @click="ocuparMesa(mesa)">
            <i class="fas fa-check mr-2"></i> Ocupar
          </button>
          <div v-if="mesa.mesa.estado === 'ocupada'" class="flex flex-wrap gap-2 justify-center w-full">
            <button
              class="flex items-center px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-semibold transition"
              @click="abrirModalCobro(mesa)">
              <i class="fas fa-cash-register mr-2"></i> Cobrar
            </button>
            <button
              class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
              @click="ocuparMesa(mesa)">
              <i class="fas fa-plus mr-2"></i> Agregar insumos
            </button>
            <button
              class="flex items-center px-4 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white font-semibold transition"
              @click="marcarInsumosEntregados(mesa)"
              v-if="mesa.insumos && mesa.insumos.some(i => i.estado !== 'entregado')">
              <i class="fas fa-check mr-2"></i> Marcar entrega
            </button>
            <button
              class="flex items-center px-4 py-2 rounded bg-red-600 hover:bg-red-800 text-white font-semibold transition"
              @click="cancelarOrden(mesa.mesa.idMesa)">
              <i class="fas fa-times mr-2"></i> Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de cobro personalizado -->
    <div v-if="mostrarModalCobro"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 backdrop-blur-custom">
      <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md">
        <h2 class="text-xl font-bold text-[#4E342E] mb-4 flex items-center">
          <i class="fas fa-cash-register mr-2 text-[#FF6F00]"></i>
          Cobrar a la mesa #{{ mesaCobro && mesaCobro.mesa ? mesaCobro.mesa.idMesa : '' }}
        </h2>
        <p class="mb-2 text-[#4E342E]">El cliente debe pagar <span class="font-bold text-[#FF6F00]">${{ mesaCobro &&
          mesaCobro.mesa ? mesaCobro.mesa.total : '' }}</span></p>
        <input type="number" min="1" v-model="cantidadPagada" placeholder="Escribe la cantidad con la que paga"
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1] mb-2" />
        <div v-if="errorCobro" class="text-red-600 mb-2">{{ errorCobro }}</div>
        <div class="flex justify-end gap-2 mt-4">
          <button class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-[#4E342E] font-semibold"
            @click="cerrarModalCobro">Cancelar</button>
          <button
            class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
            @click="confirmarCobro">Cobrar</button>
        </div>
      </div>
    </div>

    <!-- Modal de venta registrada -->
    <div v-if="mostrarModalVenta" class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-[#4E342E] mb-4 flex items-center justify-center">
          <i class="fas fa-check-circle text-green-600 mr-2"></i>
          Venta registrada
        </h2>
        <p class="text-[#4E342E] mb-4" v-html="mensajeVenta"></p>
        <button
          class="px-6 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold"
          @click="cerrarModalVenta"
        >OK</button>
      </div>
    </div>

    <!-- Loading y ticket -->
    <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
    <ticket @impreso="onImpreso" :venta="this.ventaSeleccionada" :insumos="insumosSeleccionados" :datosLocal="datos"
      :logo="logo" v-if="mostrarTicket" />
  </section>
</template>

<script>
import HttpService from '../../Servicios/HttpService'
import Ticket from '../Ventas/Ticket.vue'
import Utiles from '../../Servicios/Utiles'

export default ({
  name: "RealizarOrden",
  components: { Ticket },

  data: () => ({
    datos: {},
    logo: null,
    checkedRows: [],
    mesas: [],
    cargando: false,
    mostrarTicket: false,
    ventaSeleccionada: {},
    insumosSeleccionados: [],
    // Modal de cobro
    mostrarModalCobro: false,
    mesaCobro: null,
    cantidadPagada: '',
    errorCobro: '',
    // Modal de venta registrada
    mostrarModalVenta: false,
    mensajeVenta: ''
  }),

  mounted() {
    this.crearMesas()
    this.obtenerDatos()
  },

  methods: {
    cancelarOrden(id) {
      this.$buefy.dialog.confirm({
        title: 'Cancelar mesa ' + id,
        message: '¿Seguro deseas  cancelar la órden?',
        confirmText: 'Sí, cancelar',
        cancelText: 'No',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => {
          this.cargando = true
          HttpService.eliminar("cancelar_mesa.php", id)
            .then(resultado => {
              if (resultado) {
                this.$buefy.toast.open({
                  message: "Orden de la mesa " + id + " cancelada",
                  type: "is-success"
                })
                this.crearMesas()
                this.cargando = false
              }
            })

        }
      })
    },

    obtenerDatos() {
      HttpService.obtener("obtener_datos_local.php").then((resultado) => {
        this.datos = resultado;
        this.logo = Utiles.generarUrlImagen(this.datos.logo)
      });
    },

    onImpreso(resultado) {
      this.mostrarTicket = resultado
    },

    imprimirComprobante(venta) {
      let hoy = new Date();

      let fecha = hoy.getFullYear() + '-' + (hoy.getMonth() + 1) + '-' + hoy.getDate();

      let hora = hoy.getHours() + ":" + hoy.getMinutes() + ":" + hoy.getSeconds();

      let fechaVenta = fecha + ' ' + hora
      this.ventaSeleccionada = {
        atendio: venta.atiende,
        cliente: venta.cliente,
        fecha: fechaVenta,
        pagado: venta.pagado,
        total: venta.total
      }

      this.insumosSeleccionados = venta.insumos
      this.mostrarTicket = true
    },

    marcarInsumosEntregados(mesa) {
      this.cargando = true
      let insumos = mesa.insumos
      insumos.forEach(insumo => {
        if (insumo.estado !== "entregado") insumo.estado = "entregado"
      })

      let payload = {
        id: mesa.mesa.idMesa,
        insumos: insumos,
        total: mesa.mesa.total,
        atiende: mesa.mesa.atiende,
        idUsuario: mesa.mesa.idUsuario,
        cliente: mesa.mesa.cliente
      }

      HttpService.registrar(payload, "editar_mesa.php")
        .then(resultado => {
          if (resultado) {
            this.$buefy.toast.open({
              message: 'Insumos marcados como entregados',
              type: 'is-success'
            })
            this.crearMesas()
            this.cargando = false
          }
        })
    },

    // Nuevo: abrir modal de cobro
    abrirModalCobro(mesa) {
      this.mesaCobro = mesa;
      this.cantidadPagada = '';
      this.errorCobro = '';
      this.mostrarModalCobro = true;
    },

    cerrarModalCobro() {
      this.mostrarModalCobro = false;
      this.mesaCobro = null;
      this.cantidadPagada = '';
      this.errorCobro = '';
    },

    confirmarCobro() {
      const total = parseFloat(this.mesaCobro.mesa.total);
      const pagado = parseFloat(this.cantidadPagada);
      if (isNaN(pagado) || pagado < total) {
        this.errorCobro = 'Escribe una cantidad correcta';
        return;
      }
      this.cargando = true;
      const cambio = pagado - total;
      let payload = {
        idMesa: this.mesaCobro.mesa.idMesa,
        cliente: this.mesaCobro.mesa.cliente,
        total: total,
        pagado: pagado,
        idUsario: this.mesaCobro.mesa.idUsuario,
        insumos: this.mesaCobro.insumos,
        atiende: this.mesaCobro.mesa.atiende
      };
      HttpService.registrar(payload, "registrar_venta.php")
        .then(registrado => {
          if (registrado) {
            // Mostrar modal de venta registrada
            this.mensajeVenta = 'Gracias por su compra, su cambio <b>$' + cambio + '</b>';
            this.mostrarModalVenta = true;
            this.imprimirComprobante(payload);
            this.crearMesas();
            this.cargando = false;
            this.cerrarModalCobro();
          }
        });
    },

    cerrarModalVenta() {
      this.mostrarModalVenta = false;
      this.mensajeVenta = '';
    },

    crearMesas() {
      this.cargando = true
      HttpService.obtener("obtener_mesas.php")
        .then(resultado => {
          // Añadimos showInsumos para el collapse
          this.mesas = resultado.map(m => ({ ...m, showInsumos: false }))
          this.cargando = false
        })
    },

    ocuparMesa(mesa) {
      this.$router.push({
        name: "Ordenar",
        params: { id: mesa.mesa.idMesa, insumosEnLista: mesa.insumos, cliente: mesa.mesa.cliente },
      })
    }
  }
})
</script>