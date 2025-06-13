<template>
    <section class="container">
        <p class="title is-1 has-text-weight-bold flex items-center">
            <i class="fas fa-cogs text-4xl mr-3 text-orange-600"></i>
            Configurar
        </p>
        <div class="is-centered" v-if="mostrarIniciar">
            <p class="title is-4 has-text-weight-bold">
                Pulsa el siguiente botón para iniciar
            </p>
            <button type="button"
                class="inline-flex items-center px-6 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg transition"
                @click="iniciar">
                <i class="fas fa-play mr-2"></i>
                Iniciar
            </button>
            <ul>
                <li v-for="mensaje in mensajes" :key="mensaje">{{ mensaje }}</li>
            </ul>
        </div>

        <div v-if="mostrarDatosLocal">
            <p class="title is-4 has-text-weight-bold">
                Datos del local
            </p>
            <datos-configuracion @registrado="onDatosRegistrado" :datos="datosLocal" />
        </div>

        <div v-if="mostrarDatosUsuario">
            <p class="title is-4 has-text-weight-bold">
                Datos del usuario
            </p>
            <datos-usuario @registrado="onUsuarioRegistrado" :usuario="usuario" :roles="roles" />
        </div>
        <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>

    </section>
</template>
<script>
import DatosUsuario from '../Usuarios/DatosUsuario.vue'
import DatosConfiguracion from './DatosConfiguracion.vue'
import HttpService from '../../Servicios/HttpService'

export default {
    name: "ConfiguracionInicial",
    components: { DatosConfiguracion, DatosUsuario },

    data:()=> ({
        cargando: false,
        datosLocal: {
            nombre: "",
            telefono: "",
            numeroMesas: 1,
        },

        usuario: {
            correo: "",
            nombre: "",
            telefono: "",
            rol_id: ""
        },
        roles: [],
        mostrarIniciar: true,
        mostrarDatosLocal: false,
        mostrarDatosUsuario: false,
        mensajes: []
    }),

    methods: {
        iniciar(){
            this.cargando = true
            HttpService.obtener("crear_tablas.php")
            .then(resultado => {
                this.mensajes = resultado
                this.$buefy.toast.open({
                    message: 'Base de datos creada',
                    type: 'is-success'
                })
                this.cargando = false
                this.mostrarIniciar = false
                this.mostrarDatosLocal = true
            })
        },

        cargarRoles() {
            HttpService.obtener("obtener_roles.php")
                .then(roles => {
                    this.roles = roles
                })
        },

        onDatosRegistrado(datos){
            this.cargando = true
            this.datosLocal = datos
            HttpService.registrar(this.datosLocal, "registrar_datos_local.php")
            .then(registrado => {
                if(registrado) {
                    this.$buefy.toast.open({
                        message: 'Información registrada',
                        type: 'is-success'
                    })
                    this.cargando = false
                    this.mostrarDatosLocal = false
                    this.cargarRoles() // Cargar roles antes de mostrar el formulario de usuario
                    this.mostrarDatosUsuario = true
                }
            })
        },

        onUsuarioRegistrado(datos){
            this.cargando = true
            this.usuario = datos
            HttpService.registrar(this.usuario, "registrar_usuario.php")
            .then(registrado => {
                if(registrado) {
                    this.$buefy.toast.open({
                        message: 'Usuario registrado. Recuerda que la contraseña por defecto es 0000(Cuatro ceros)',
                        type: 'is-success'
                    })
                    this.cargando = false
                    this.$emit('configurado')
                }
            })
        },
    }
}
</script>