<template>
    <div class="min-h-screen bg-[#FFF8F1] p-6">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div class="flex items-center">
                <i class="fas fa-archive text-3xl mr-3 text-[#FF6F00]"></i>
                <h1 class="text-2xl font-bold text-[#4E342E]">Categorías</h1>
            </div>
            <button
                class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition mt-4 md:mt-0"
                @click="abrirModal('registra')">
                <i class="fas fa-plus mr-2"></i>
                Añadir categoría
            </button>
        </div>

        <!-- Select de paginación -->
        <div class="mb-4">
            <select v-model="perPage"
                class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1] max-w-xs">
                <option value="5">5 por página</option>
                <option value="10">10 por página</option>
                <option value="15">15 por página</option>
                <option value="20">20 por página</option>
            </select>
        </div>

        <!-- Tabla de categorías -->
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2"></th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Descripción</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="cat in categoriasPaginadas" :key="cat.id" class="hover:bg-[#FFF8F1]">
                        <td class="px-4 py-2">
                            <i v-if="cat.tipo === 'PLATILLO'" class="fas fa-bowl-food text-[#FF6F00]"></i>
                            <i v-if="cat.tipo === 'BEBIDA'" class="fas fa-mug-saucer text-[#FF6F00]"></i>
                        </td>
                        <td class="px-4 py-2 text-[#4E342E]">{{ cat.tipo }}</td>
                        <td class="px-4 py-2 text-[#4E342E]">{{ cat.nombre }}</td>
                        <td class="px-4 py-2 text-[#4E342E]">{{ cat.descripcion }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <button
                                class="flex items-center px-3 py-1 rounded bg-red-600 hover:bg-red-800 text-white text-xs font-semibold transition"
                                @click="eliminar(cat)">
                                <i class="fas fa-trash mr-1"></i> Eliminar
                            </button>
                            <button
                                class="flex items-center px-3 py-1 rounded bg-blue-600 hover:bg-blue-800 text-white text-xs font-semibold transition"
                                @click="editar(cat)">
                                <i class="fas fa-pen mr-1"></i> Editar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Paginación simple -->
            <div class="flex justify-center mt-4 gap-2">
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

        <modal-categoria :visible.sync="mostrarModalCategoria" @registrado="onRegistrado" :categoria="categoria"
            :titulo="titulo" :tipo="tipo" />

        <!-- Loading -->
        <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
    </div>
</template>

<script>
import HttpService from '../../Servicios/HttpService'
import ModalCategoria from './ModalCategoria.vue'
export default {
    name: 'Categorias',
    components: { ModalCategoria },
    data() {
        return {
            mostrarModalCategoria: false,
            estaRegistrando: false,
            estaEditando: false,
            titulo: "",
            tipo: "",
            categoria: {
                tipo: "",
                nombre: "",
                descripcion: ""
            },
            categorias: [],
            isPaginated: true,
            isPaginationSimple: false,
            isPaginationRounded: true,
            paginationPosition: 'bottom',
            defaultSortDirection: 'asc',
            sortIcon: 'arrow-up',
            sortIconSize: 'is-small',
            currentPage: 1,
            perPage: 10,
            cargando: false
        }
    },

    computed: {
        totalPages() {
            return Math.ceil(this.categorias.length / this.perPage) || 1;
        },
        categoriasPaginadas() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.categorias.slice(start, start + parseInt(this.perPage));
        }
    },

    created() {
        this.obtenerCategorias()
    },

    methods: {
        eliminar(categoria) {
            this.$buefy.dialog.confirm({
                title: 'Eliminar categoría ' + categoria.nombre,
                message: '¿Seguro que deseas eliminar la categoría? Esta acción no se puede deshacer',
                confirmText: 'Sí, eliminar',
                cancelText: 'No, salir',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => {
                    this.cargando = true
                    HttpService.eliminar("eliminar_categoria.php", categoria.id)
                        .then(eliminado => {
                            if (eliminado) {
                                this.obtenerCategorias()
                                this.$buefy.toast.open('Categoría eliminada')
                                this.cargando = false
                            }
                        })
                }
            })
        },

        editar(categoria) {
            this.abrirModal("edita", categoria)
        },

        abrirModal(tipo, categoria = {}) {
            this.categoria = { ...categoria }
            this.mostrarModalCategoria = true
            if (tipo === "registra") {
                this.tipo = tipo
                this.titulo = "Agregar"
            }
            if (tipo === "edita") {
                this.tipo = tipo
                this.titulo = "Editar"
            }
        },

        onRegistrado() {
            this.obtenerCategorias()
            this.categoria = {
                tipo: "",
                nombre: "",
                descripcion: ""
            }
            this.mostrarModalCategoria = false
        },

        obtenerCategorias() {
            this.cargando = true
            HttpService.obtener("obtener_categorias.php")
                .then(resultado => {
                    this.categorias = resultado
                    this.cargando = false
                })
        }
    }
}
</script>