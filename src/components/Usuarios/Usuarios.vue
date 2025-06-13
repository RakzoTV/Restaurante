<template>
    <div class="min-h-screen bg-[#FFF8F1] p-6">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div class="flex items-center mb-4 md:mb-0">
                <i class="fas fa-users text-3xl mr-3" style="color:#FF6F00"></i>
                <div>
                    <h1 class="text-2xl font-bold" style="color:#4E342E;">Usuarios</h1>
                    <span class="text-[#4E342E] text-base">{{ usuarios.length }} resultados</span>
                </div>
            </div>
            <button
                class="flex items-center px-5 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold transition"
                @click="abrirModalRegistro">
                <i class="fas fa-user-plus mr-2"></i>
                Añadir usuario
            </button>
        </div>

        <!-- Filtros superiores -->
        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
            <select v-model="perPage"
                class="border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1] max-w-xs">
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
                <input type="text" v-model="busqueda" @input="filtrarUsuarios" placeholder="Buscar usuario..."
                    class="w-full pl-10 pr-4 py-2 border rounded focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-white" />
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr style="color:#4E342E;" class="text-left">
                        <th class="py-2 px-3">Correo</th>
                        <th class="py-2 px-3">Nombre</th>
                        <th class="py-2 px-3">Teléfono</th>
                        <th class="py-2 px-3">Rol</th>
                        <th class="py-2 px-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="usuario in usuariosPaginados" :key="usuario.id" class="hover:bg-[#FFF8F1]">
                        <td class="py-2 px-3">{{ usuario.correo }}</td>
                        <td class="py-2 px-3">{{ usuario.nombre }}</td>
                        <td class="py-2 px-3">{{ usuario.telefono }}</td>
                        <td class="py-2 px-3">
                            {{ obtenerNombreRol(usuario.rol_id) }}
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex gap-2">
                                <button
                                    class="flex items-center px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition"
                                    @click="eliminar(usuario)">
                                    <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                </button>
                                <button
                                    class="flex items-center px-3 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition"
                                    @click="abrirModalEdicion(usuario)">
                                    <i class="fas fa-pen mr-1"></i> Editar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Paginación -->
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

        <modal-usuarios :visible.sync="mostrarModalUsuario" :usuario="usuarioSeleccionado" :roles="roles"
            :es-edicion="esEdicion" @registrado="onRegistradoUsuario" />

        <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
    </div>
</template>

<script>
import HttpService from '../../Servicios/HttpService'
import ModalUsuarios from './ModalUsuarios.vue'

export default {
    name: "Usuarios",
    components: { ModalUsuarios },

    data: () => ({
        usuarios: [],
        usuariosFiltrados: [],
        busqueda: "",
        cargando: false,
        currentPage: 1,
        perPage: 10,
        mostrarModalUsuario: false,
        usuarioSeleccionado: {},
        esEdicion: false,
        roles: []
    }),

    computed: {
        totalPages() {
            return Math.ceil(this.usuariosFiltrados.length / this.perPage) || 1;
        },
        usuariosPaginados() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.usuariosFiltrados.slice(start, start + parseInt(this.perPage));
        }
    },

    mounted() {
        this.obtenerUsuarios();
        this.obtenerRoles();
    },

    methods: {
        obtenerNombreRol(idRol) {
            const rol = this.roles.find(r => r.id === idRol);
            return rol ? rol.nombre : '';
        },
        abrirModalRegistro() {
            this.usuarioSeleccionado = {};
            this.esEdicion = false;
            this.mostrarModalUsuario = true;
        },
        abrirModalEdicion(usuario) {
            this.usuarioSeleccionado = { ...usuario };
            this.esEdicion = true;
            this.mostrarModalUsuario = true;
        },
        onRegistradoUsuario() {
            this.obtenerUsuarios();
            this.$buefy.toast.open({
                message: 'Usuario guardado correctamente',
                type: 'is-success'
            });
        },
        eliminar(usuario) {
            this.$buefy.dialog.confirm({
                title: "Eliminar el usuario " + usuario.nombre,
                message:
                    "¿Seguro que deseas eliminar el usuario? Esta acción no se puede deshacer",
                confirmText: "Sí, eliminar",
                cancelText: "No, salir",
                type: "is-danger",
                hasIcon: true,
                onConfirm: () => {
                    this.cargando = true;
                    HttpService.eliminar("eliminar_usuario.php", usuario.id).then(
                        (eliminado) => {
                            if (eliminado) {
                                this.obtenerUsuarios();
                                this.$buefy.toast.open("Usuario eliminado");
                            }
                            this.cargando = false;
                        }
                    );
                },
            });
        },
        obtenerUsuarios() {
            this.cargando = true
            HttpService.obtener("obtener_usuarios.php")
                .then(resultado => {
                    this.usuarios = resultado
                    this.usuariosFiltrados = resultado
                    this.cargando = false
                })
        },
        obtenerRoles() {
            HttpService.obtener("obtener_roles.php")
                .then(resultado => {
                    this.roles = resultado
                })
        },
        filtrarUsuarios() {
            const texto = this.busqueda.toLowerCase()
            this.usuariosFiltrados = this.usuarios.filter(u =>
                u.correo.toLowerCase().includes(texto) ||
                u.nombre.toLowerCase().includes(texto) ||
                u.telefono.toLowerCase().includes(texto) ||
                this.obtenerNombreRol(u.rol_id).toLowerCase().includes(texto)
            )
            this.currentPage = 1
        }
    }
}
</script>