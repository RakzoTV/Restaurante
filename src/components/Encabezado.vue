<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div :class="[
            'flex flex-col transition-all duration-200 bg-[#FFF8F1] border-r border-gray-200',
            expandido ? 'w-56' : 'w-16'
        ]">
            <!-- Header/logo -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center">
                    <img :src="logo" alt="logo" class="h-8 w-8 mr-2" />
                    <span v-if="expandido" class="font-bold text-[#4E342E] text-lg">{{ datosLocal.nombre }}</span>
                </div>
                <button class="text-[#4E342E] focus:outline-none" @click="expandido = !expandido">
                    <i :class="expandido ? 'fas fa-angle-left' : 'fas fa-angle-right'"></i>
                </button>
            </div>
            <!-- Navigation -->
            <nav class="flex-1 mt-4">
                <router-link v-for="item in items" :key="item.to" :to="item.to"
                    class="flex items-center px-4 py-2 text-[#4E342E] hover:bg-[#FFECB3] hover:text-[#FF6F00] rounded transition"
                    active-class="bg-[#FFECB3] text-[#FF6F00]">
                    <i :class="item.icon" class="text-lg"></i>
                    <span v-if="expandido" class="ml-3">{{ item.label }}</span>
                </router-link>
            </nav>
            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 mt-auto">
                <div v-if="expandido" class="mb-2">
                    <button class="w-full flex items-center justify-center py-2 rounded font-semibold transition"
                        style="background-color: #FF6F00; color: white;" @mouseover="hoverPerfil = true"
                        @mouseleave="hoverPerfil = false"
                        :style="hoverPerfil ? { backgroundColor: '#E65100', color: 'white' } : { backgroundColor: '#FF6F00', color: 'white' }"
                        @click="irAPerfil">
                        <i class="fas fa-user mr-2"></i>
                        {{ nombreUsuario }}
                    </button>
                </div>
                <button class="w-full py-2 rounded text-white font-semibold transition flex items-center justify-center"
                    :style="{ backgroundColor: hoverSalir ? '#E65100' : '#FF6F00' }" @mouseover="hoverSalir = true"
                    @mouseleave="hoverSalir = false" @click="salir">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span v-if="expandido">Salir</span>
                </button>
            </div>
        </div>
        <!-- Main content -->
        <div class="flex-1 bg-[#FFF8F1] overflow-auto h-screen">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import HttpService from '../Servicios/HttpService'
import Utiles from '../Servicios/Utiles'

export default {
    name: 'Encabezado',
    data: () => ({
        datosLocal: {},
        nombreUsuario: "",
        logo: null,
        expandido: true,
        hoverSalir: false,
        hoverPerfil: false,
        items: [
            { to: '/', icon: 'fas fa-home', label: 'Inicio' },
            { to: '/insumos', icon: 'fas fa-utensils', label: 'Insumos' },
            { to: '/realizar-orden', icon: 'fas fa-receipt', label: 'Ordenar' },
            { to: '/categorias', icon: 'fas fa-archive', label: 'Categorías' },
            { to: '/usuarios', icon: 'fas fa-users', label: 'Usuarios' },
            { to: '/reporte-ventas', icon: 'fas fa-cash-register', label: 'Ventas' },
            { to: '/configurar', icon: 'fas fa-cog', label: 'Configurar' }
        ]
    }),

    mounted() {
        this.obtenerDatos()
        this.nombreUsuario = localStorage.getItem('nombreUsuario')
        this.logo = Utiles.generarUrlImagen(this.datosLocal.logo)
    },

    methods: {
        obtenerDatos() {
            HttpService.obtener("obtener_datos_local.php")
                .then(resultado => {
                    this.datosLocal = resultado
                    this.logo = Utiles.generarUrlImagen(this.datosLocal.logo)
                })
        },
        salir() {
            this.$buefy.dialog.confirm({
                title: '¿Salir de la aplicación?',
                message: 'Deseas salir',
                confirmText: 'Sí, salir',
                cancelText: 'No',
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => {
                    this.$emit("cerrar", false)
                    localStorage.removeItem('logeado')
                    this.$buefy.toast.open('Hasta pronto')
                }
            })
        },
        irAPerfil() {
            this.$router.push('/perfil')
        }
    }
}
</script>