<template>
  <section class="min-h-screen bg-[#FFF8F1] p-6">
    <!-- Botón volver -->
    <button
    class="flex items-center mb-8 px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
    @click="$router.back()" type="button">
    <i class="fas fa-arrow-left mr-2"></i>
    Volver
  </button>
  <!-- Título y cliente-->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div class="flex items-center mb-4 md:mb-0">
      <i class="fas fa-pen text-3xl mr-3 text-[#FF6F00]"></i>
      <h1 class="text-2xl font-bold text-[#4E342E] ml-2">
        Tomar orden para la mesa #{{ idMesa }}
      </h1>
    </div>
    <div class="flex flex-col md:flex-row md:items-center gap-2">
      <label class="text-[#4E342E] font-semibold mr-2">Nombre del cliente (Opcional):</label>
      <input class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-white"
        placeholder="Ej. Don Paco" v-model="cliente" />
    </div>
  </div>

    <!-- Total y botón de acción -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
      <span class="text-3xl font-bold text-[#FF6F00]">${{ total }}</span>
      <div class="flex gap-2 mt-4 md:mt-0">
        <button v-if="!estaAgregandoInsumos"
          class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
          @click="realizarOrden">
          <i class="fas fa-basket-shopping mr-2"></i>
          Ordenar
        </button>
        <button v-if="estaAgregandoInsumos"
          class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
          @click="editarOrden">
          <i class="fas fa-plus mr-2"></i>
          Añadir
        </button>
      </div>
    </div>

    <!-- Buscador de insumos -->
    <div class="mb-6">
      <div class="relative w-full md:w-1/2">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#4E342E]">
          <i class="fas fa-search"></i>
        </span>
        <input id="busqueda" type="text" v-model="nombre" @input="buscarInsumo"
          placeholder="Nombre del platillo o bebida"
          class="w-full pl-10 pr-4 py-2 border rounded focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-white" />
        <div v-if="filteredDataObj.length > 0 && nombre"
          class="absolute z-10 w-full bg-white border rounded shadow mt-1">
          <div v-for="option in filteredDataObj" :key="option.id" @click="agregarInsumoAOrden(option)"
            class="px-4 py-2 cursor-pointer hover:bg-[#FFF8F1] text-[#4E342E]">
            {{ option.nombre }}
          </div>
        </div>
      </div>
    </div>

    <!-- Insumos agregados y servidos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="col-span-2">
        <div v-if="insumosOrden.length > 0" class="mb-4">
          <h2 class="text-lg font-bold text-[#4E342E] flex items-center mb-2">
            <i class="fas fa-plus mr-2"></i> Insumos agregados
          </h2>
          <productos-orden :lista="insumosOrden" :tipo="'nuevo'" @modificado="onProductoModificado"
            @quitar="eliminar" />
        </div>
        <div v-if="insumosAnteriores.length > 0" class="mb-4">
          <h2 class="text-lg font-bold text-[#4E342E] flex items-center mb-2">
            <i class="fas fa-basket-shopping mr-2"></i> Insumos servidos
          </h2>
          <productos-orden :lista="insumosAnteriores" :tipo="'entregado'" />
        </div>
      </div>

      <!-- Sugerencias -->
      <div v-if="insumos.length > 0">
        <h3 class="text-base font-bold text-[#4E342E] mb-2">También te puede interesar</h3>
        <div class="space-y-4">
          <div v-for="insumo in insumos" :key="insumo.id" class="bg-white rounded-xl shadow p-4 flex flex-col">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center text-sm text-[#4E342E] font-semibold">
                <i v-if="insumo.tipo === 'PLATILLO'" class="fas fa-bowl-food text-[#FF6F00] mr-2"></i>
                <i v-if="insumo.tipo === 'BEBIDA'" class="fas fa-mug-saucer text-[#FF6F00] mr-2"></i>
                {{ insumo.nombre }}
              </div>
              <button
                class="flex items-center px-2 py-1 rounded bg-[#FF6F00] hover:bg-[#E65100] text-white text-xs font-semibold transition"
                @click="agregarInsumoAOrden(insumo)">
                <i class="fas fa-plus"></i>
              </button>
            </div>
            <div class="text-xs text-[#4E342E] mb-2">{{ insumo.descripcion }}</div>
            <div class="text-center font-bold text-[#4E342E]">${{ insumo.precio }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import HttpService from "../../Servicios/HttpService";
import ProductosOrden from "./ProductosOrden.vue";
export default {
  name: "Ordenar",
  components: { ProductosOrden },
  data: () => ({
    idMesa: "",
    insumos: [],
    nombre: "",
    insumosAnteriores: [],
    insumosOrden: [],
    total: 0,
    cliente: "",
    estaAgregandoInsumos: false,
  }),

  mounted() {
    this.$nextTick(() => {
      const input = document.querySelector("#busqueda");
      if (input) input.focus();
    });
    this.idMesa = this.$route.params.id;
    this.cliente = (this.$route.params.cliente) ? this.$route.params.cliente : "";
    this.insumosAnteriores = this.$route.params.insumosEnLista;
    if (this.insumosAnteriores.length > 0) {
      this.calcularTotal();
      this.estaAgregandoInsumos = true;
    }
  },

  methods: {
    onProductoModificado() {
      this.calcularTotal()
    },
    editarOrden() {
      let insumos = this.insumosAnteriores.concat(this.insumosOrden);
      let payload = {
        id: this.idMesa,
        insumos: insumos,
        total: this.total,
        atiende: localStorage.getItem("nombreUsuario"),
        idUsuario: localStorage.getItem("idUsuario"),
        cliente: this.cliente,
      };

      HttpService.registrar(payload, "editar_mesa.php").then((resultado) => {
        if (resultado) {
          this.$buefy.toast.open({
            message: "Insumos agregados",
            type: "is-success",
          });
          this.$router.push({
            name: "RealizarOrden",
          });
        }
      });
    },

    realizarOrden() {
      let payload = {
        id: this.idMesa,
        insumos: this.insumosOrden,
        total: this.total,
        atiende: localStorage.getItem("nombreUsuario"),
        idUsuario: localStorage.getItem("idUsuario"),
        cliente: this.cliente,
      };

      HttpService.registrar(payload, "ocupar_mesa.php").then((resultado) => {
        if (resultado) {
          this.$buefy.toast.open({
            message: "Insumos agregados",
            type: "is-success",
          });
          this.$router.push({
            name: "RealizarOrden",
          });
        }
      });
    },

    eliminar(idInsumo) {
      let lista = this.insumosOrden;
      for (let i = 0; i < lista.length; i++) {
        if (lista[i].id === idInsumo) {
          lista.splice(i, 1);
        }
      }
      this.insumosOrden = lista;
      this.calcularTotal();
    },

    calcularTotal() {
      let total = 0;
      let totalAnterior = 0;
      if (this.insumosAnteriores.length > 0) {
        this.insumosAnteriores.forEach((insumo) => {
          totalAnterior +=
            parseFloat(insumo.cantidad) * parseFloat(insumo.precio);
        });
      }
      this.insumosOrden.forEach((insumo) => {
        total += parseFloat(insumo.cantidad) * parseFloat(insumo.precio);
      });
      this.total = total + totalAnterior;
    },

    buscarInsumo() {
      if (this.nombre) {
        HttpService.obtenerConDatos(
          this.nombre,
          "obtener_insumo_nombre.php"
        ).then((resultado) => {
          this.insumos = resultado;
        });
      }
    },

    agregarInsumoAOrden(insumo) {
      if (insumo) {
        let indice = this.verificarSiExisteEnLista(insumo.id);
        if (indice >= 0) {
          this.aumentarCantidad(indice);
        } else {
          this.insumosOrden.push({
            id: insumo.id,
            codigo: insumo.codigo,
            nombre: insumo.nombre,
            precio: insumo.precio,
            caracteristicas: "",
            cantidad: 1,
            estado: "pendiente",
          });
        }
        setTimeout(() => (this.nombre = ""), 10);
        this.calcularTotal();
      }
    },

    verificarSiExisteEnLista(idInsumo) {
      let lista = this.insumosOrden;
      for (let i = 0; i < lista.length; i++) {
        if (lista[i].id === idInsumo) return i;
      }
      return -1;
    },

    aumentarCantidad(indice) {
      let lista = this.insumosOrden;
      let insumo = lista[indice];

      insumo.cantidad++;

      lista[indice] = insumo;
      this.insumosOrden = lista;
    },
  },

  computed: {
    filteredDataObj() {
      return this.insumos.filter((option) => {
        return (
          option.nombre
            .toString()
            .toLowerCase()
            .indexOf(this.nombre.toLowerCase()) >= 0
        );
      });
    },
  },
};
</script>