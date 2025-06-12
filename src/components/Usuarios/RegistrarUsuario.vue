<template>
    <section>
        <p class="title is-1 has-text-weight-bold">
            <b-icon
                icon="account-multiple-plus"
                size="is-large"
                type="is-primary">
            </b-icon>
            Registrar usuario
        </p> 
         <b-breadcrumb align="is-left" >
            <b-breadcrumb-item tag='router-link' to="/">Inicio</b-breadcrumb-item>
            <b-breadcrumb-item tag='router-link' to="/usuarios">Usuarios</b-breadcrumb-item>
        </b-breadcrumb>
        <datos-usuario @registrado="onRegistrado" :usuario="usuario" :roles="roles"></datos-usuario>
        <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>

    </section>
</template>
<script>
import DatosUsuario from './DatosUsuario.vue'
import HttpService from '../../Servicios/HttpService'

export default ({
    name: "RegistrarUsuario",
    components : { DatosUsuario },

    data: () => ({
        usuario: {
            correo: "",
            nombre: "",
            telefono: "",
            rol_id: "" // Agrega el campo rol_id
        },
        cargando: false,
        roles: [] // Lista de roles
    }),

    mounted() {
        this.obtenerRoles()
    },

    methods: {
        onRegistrado(usuario){
            this.cargando = true
            this.usuario = usuario
            HttpService.registrar(this.usuario, "registrar_usuario.php")
            .then(registrado => {
                if(registrado) {
                    this.usuario = {
                        correo: "",
                        nombre: "",
                        telefono: "",
                        rol_id: ""
                    }
                    this.$buefy.toast.open({
                        message: 'Usuario registrado. Recuerda que la contraseña por defecto es 0000',
                        type: 'is-success'
                    })
                    this.cargando = false
                }
            })
        },
        obtenerRoles() {
            HttpService.obtener("obtener_roles.php")
                .then(roles => {
                    this.roles = roles
                })
        }
    }
})
</script>