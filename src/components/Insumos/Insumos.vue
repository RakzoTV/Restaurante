<template>
  <div class="min-h-screen bg-[#FFF8F1] p-6">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
      <div class="flex items-center mb-4 md:mb-0">
        <i class="fas fa-utensils text-3xl mr-3" style="color:#FF6F00"></i>
        <div>
          <h1 class="text-2xl font-bold" style="color:#4E342E;">Insumos</h1>
          <span class="text-[#4E342E] text-base">{{ insumos.length }} resultados</span>
        </div>
      </div>
      <!-- Botón para abrir el modal de agregar insumo -->
      <button
        class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
        @click="abrirModalRegistro">
        <i class="fas fa-plus mr-2"></i>
        Añadir insumo
      </button>
    </div>

    <!-- Filtros superiores -->
    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
      <button
        class="flex items-center px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition z-10"
        @click="filtrar = !filtrar" type="button">
        <i class="fas fa-filter mr-2"></i>
        {{ filtrar ? 'Ocultar filtros' : 'Filtrar' }}
      </button>
      <select v-model="perPage"
        class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] max-w-xs">
        <option value="5">5 por página</option>
        <option value="10">10 por página</option>
        <option value="15">15 por página</option>
        <option value="20">20 por página</option>
      </select>
    </div>

    <!-- Buscador general -->
    <div class="flex items-center mb-4">
      <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#4E342E]">
          <i class="fas fa-search"></i>
        </span>
        <input type="text" v-model="filtros.nombre" @input="busquedaAvanzada" placeholder="Buscar insumo..."
          class="w-full pl-10 pr-4 py-2 border rounded focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-white" />
      </div>
    </div>

    <!-- Filtros avanzados -->
    <div v-if="filtrar" class="bg-white rounded-xl shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-1 font-semibold" style="color:#4E342E;">Tipo de insumo</label>
          <select v-model="filtros.tipo" @change="busquedaAvanzada"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E]">
            <option value="">Selecciona el tipo de insumo</option>
            <option value="PLATILLO">Platillo</option>
            <option value="BEBIDA">Bebida</option>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-semibold" style="color:#4E342E;">Categoría</label>
          <select v-model="filtros.categoria" @change="busquedaAvanzada"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E]">
            <option value="">Selecciona la categoría</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-semibold" style="color:#4E342E;">Nombre del insumo</label>
          <input type="text" placeholder="Nombre del insumo que deseas buscar" v-model="filtros.nombre"
            @input="busquedaAvanzada"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E]" />
        </div>
      </div>
    </div>

    <!-- Tabla de insumos -->
    <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr style="color:#4E342E;" class="text-left">
            <th class="py-2 px-3"></th>
            <th class="py-2 px-3">Tipo</th>
            <th class="py-2 px-3">Código</th>
            <th class="py-2 px-3">Nombre</th>
            <th class="py-2 px-3">Descripción</th>
            <th class="py-2 px-3">Categoría</th>
            <th class="py-2 px-3">Precio</th>
            <th class="py-2 px-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="insumo in insumosPaginados" :key="insumo.id" class="hover:bg-[#FFF8F1]">
            <td class="py-2 px-3">
              <i v-if="insumo.tipo === 'PLATILLO'" class="fas fa-hamburger text-[#FF6F00]"></i>
              <i v-if="insumo.tipo === 'BEBIDA'" class="fas fa-coffee text-[#4E342E]"></i>
            </td>
            <td class="py-2 px-3">{{ insumo.tipo }}</td>
            <td class="py-2 px-3">{{ insumo.codigo }}</td>
            <td class="py-2 px-3">{{ insumo.nombre }}</td>
            <td class="py-2 px-3">{{ insumo.descripcion }}</td>
            <td class="py-2 px-3">{{ insumo.categoria }}</td>
            <td class="py-2 px-3">${{ insumo.precio }}</td>
            <td class="py-2 px-3">
              <div class="flex gap-2">
                <button
                  class="flex items-center px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition"
                  @click="eliminar(insumo)">
                  <i class="fas fa-trash-alt mr-1"></i> Eliminar
                </button>
                <button
                  class="flex items-center px-3 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition"
                  @click="editar(insumo.id)">
                  <i class="fas fa-pen mr-1"></i> Editar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Paginación similar a Categorías -->
      <div class="flex justify-center mt-4 gap-2">
        <button
          class="px-3 py-1 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="text-[#4E342E] font-semibold px-2">Página {{ currentPage }}</span>
        <button
          class="px-3 py-1 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>

    <!-- Modal para registrar insumo -->
    <modal-insumo :visible.sync="modalRegistro" :esEdicion="false" :insumo="nuevoInsumo"
      @registrado="recargarInsumos" />

    <!-- Modal para editar insumo -->
    <modal-insumo :visible.sync="modalEditar" :esEdicion="true" :insumo="insumoEditar" @registrado="actualizarInsumo" />
  </div>
</template>

<script>
import HttpService from "../../Servicios/HttpService";
import ModalInsumo from "./ModalInsumo.vue";

export default {
  name: "Insumos",
  components: { ModalInsumo },

  data: () => ({
    filtrar: false,
    hoverFiltrar: false,
    insumos: [],
    filtros: {
      tipo: "",
      categoria: "",
      nombre: "",
    },
    categorias: [],
    cargando: false,
    currentPage: 1,
    perPage: 10,
    modalRegistro: false,
    modalEditar: false,
    nuevoInsumo: {
      tipo: "",
      codigo: "",
      nombre: "",
      descripcion: "",
      categoria: "",
      precio: ""
    },
    insumoEditar: {
      id: "",
      tipo: "",
      codigo: "",
      nombre: "",
      descripcion: "",
      categoria: "",
      precio: ""
    }
  }),

  computed: {
    totalPages() {
      return Math.ceil(this.insumos.length / this.perPage) || 1;
    },
    insumosPaginados() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.insumos.slice(start, start + parseInt(this.perPage));
    }
  },

  watch: {
    perPage() {
      this.currentPage = 1;
    },
    insumos() {
      if (this.currentPage > this.totalPages) {
        this.currentPage = this.totalPages;
      }
    }
  },

  mounted() {
    this.obtenerInsumos();
  },

  methods: {
    abrirModalRegistro() {
      this.nuevoInsumo = {
        tipo: "",
        codigo: "",
        nombre: "",
        descripcion: "",
        categoria: "",
        precio: ""
      };
      this.modalRegistro = true;
    },

    abrirModalEditar(insumo) {
      this.insumoEditar = { ...insumo };
      this.modalEditar = true;
    },

    recargarInsumos(insumo) {
      this.cargando = true;
      HttpService.registrar(insumo, "registrar_insumo.php")
        .then(registrado => {
          if (registrado) {
            this.$buefy.toast.open("Insumo registrado correctamente");
            this.obtenerInsumos();
            this.modalRegistro = false;
          } else {
            this.$buefy.toast.open({
              message: "No se pudo registrar el insumo.",
              type: "is-danger"
            });
          }
          this.cargando = false;
        });
    },

    actualizarInsumo(insumo) {
      this.cargando = true;
      HttpService.registrar(insumo, "editar_insumo.php")
        .then(actualizado => {
          if (actualizado) {
            this.$buefy.toast.open("Insumo actualizado correctamente");
            this.obtenerInsumos();
            this.modalEditar = false;
          } else {
            this.$buefy.toast.open({
              message: "No se pudo actualizar el insumo.",
              type: "is-danger"
            });
          }
          this.cargando = false;
        });
    },

    busquedaAvanzada() {
      if (this.filtros.tipo === "BEBIDA" || this.filtros.tipo === "PLATILLO") {
        this.obtenerCategorias();
      }
      if (this.filtros.tipo === "") {
        this.categorias = [];
        this.filtros.categoria = "";
      }
      this.obtenerInsumos();
    },

    eliminar(insumo) {
      this.$buefy.dialog.confirm({
        title: "Eliminar el insumo " + insumo.nombre,
        message:
          "¿Seguro que deseas eliminar el insumo? Esta acción no se puede deshacer",
        confirmText: "Sí, eliminar",
        cancelText: "No, salir",
        type: "is-danger",
        hasIcon: true,
        onConfirm: () => {
          HttpService.eliminar("eliminar_insumo.php", insumo.id).then(
            (eliminado) => {
              if (eliminado) {
                this.obtenerInsumos();
                this.$buefy.toast.open("Insumo eliminado");
              }
            }
          );
        },
      });
    },

    editar(idInsumo) {
      const insumo = this.insumos.find(i => i.id === idInsumo);
      if (insumo) {
        this.abrirModalEditar(insumo);
      }
    },

    obtenerInsumos() {
      this.cargando = true;
      HttpService.obtenerConDatos(this.filtros, "obtener_insumos.php").then(
        (datos) => {
          this.insumos = datos || [];
          this.cargando = false;
        }
      );
    },

    obtenerCategorias() {
      HttpService.obtenerConDatos(
        this.filtros.tipo,
        "obtener_categorias_tipo.php"
      ).then((datos) => {
        this.categorias = datos;
      });
    },
  },
};
</script>