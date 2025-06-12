<template>
    <section class="min-h-screen flex items-center justify-center" style="background-color: #FFF8F1;">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold mb-2" style="color: #4E342E;">Iniciar sesión</h1>
                <p class="text-base" style="color: #4E342E;">Sistema para restaurantes o venta de comida</p>
            </div>
            <form @submit.prevent="ingresar">
                <div class="mb-5">
                    <label class="block mb-1" for="correo" style="color: #4E342E;">Correo electrónico</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 pointer-events-none text-[#4E342E]">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            id="correo"
                            type="email"
                            v-model="correo"
                            placeholder="Correo electrónico"
                            class="w-full pl-12 pr-4 py-2 border rounded-lg focus:outline-none"
                            style="color: #4E342E; border-color: #FF6F00;"
                            @focus="e => e.target.style.boxShadow='0 0 0 2px #FF6F00'"
                            @blur="e => e.target.style.boxShadow='none'"
                            autocomplete="username"
                        />
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block mb-1" for="password" style="color: #4E342E;">Contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 pointer-events-none text-[#4E342E]">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            :type="mostrarPassword ? 'text' : 'password'"
                            id="password"
                            v-model="password"
                            placeholder="Contraseña"
                            class="w-full pl-12 pr-10 py-2 border rounded-lg focus:outline-none"
                            style="color: #4E342E; border-color: #FF6F00;"
                            @focus="e => e.target.style.boxShadow='0 0 0 2px #FF6F00'"
                            @blur="e => e.target.style.boxShadow='none'"
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            @click="mostrarPassword = !mostrarPassword"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-[#4E342E] focus:outline-none"
                            tabindex="-1"
                        >
                            <i :class="mostrarPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button
                        type="submit"
                        class="w-full py-2 px-4 font-semibold rounded-lg shadow-md transition duration-200 flex items-center justify-center"
                        :disabled="cargando"
                        style="background-color: #FF6F00; color: white;"
                        @mouseover="e => e.target.style.backgroundColor='#E65100'"
                        @mouseleave="e => e.target.style.backgroundColor='#FF6F00'"
                    >
                        <span v-if="!cargando"><i class="fas fa-sign-in-alt mr-2"></i>Ingresar</span>
                        <span v-else><i class="fas fa-spinner fa-spin mr-2"></i>Cargando...</span>
                    </button>
                </div>
            </form>
            <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
        </div>
    </section>
</template>

<script>
import HttpService from '../../Servicios/HttpService'

export default {
    name: "Login",

    data: () => ({
        correo: "",
        password: "",
        cargando: false,
        mostrarPassword: false
    }),

    methods: {
        ingresar() {
            if (!this.correo) {
                this.$buefy.toast.open({
                    message: 'Debes colocar el correo',
                    type: 'is-warning'
                })
                return
            }
            if (!this.password) {
                this.$buefy.toast.open({
                    message: 'Debes colocar la contraseña',
                    type: 'is-warning'
                })
                return
            }
            this.cargando = true
            let payload = {
                correo: this.correo,
                password: this.password
            }

            HttpService.obtenerConDatos(payload, "iniciar_sesion.php")
                .then(log => {
                    if (log.resultado === "cambia") {
                        this.$buefy.toast.open({
                            message: 'Datos correctos. Debes cambiar tu contraseña',
                            type: 'is-info'
                        })
                        this.$emit("logeado", log)
                        this.cargando = false
                        return
                    }

                    if (log.resultado) {
                        this.$buefy.toast.open({
                            message: 'Datos correctos. Bienvenido',
                            type: 'is-success'
                        })
                        this.$emit("logeado", log)
                        this.cargando = false
                    } else {
                        this.$buefy.toast.open({
                            message: 'Datos incorrectos. Verifica tu información',
                            type: 'is-danger'
                        })
                        this.cargando = false
                    }
                })

        }
    }

}
</script>