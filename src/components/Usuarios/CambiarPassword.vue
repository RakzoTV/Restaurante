<template>
  <section class="max-w-lg mx-auto bg-[#FFF8F1] rounded-xl shadow-lg p-8 mt-10">
    <div class="flex items-center mb-6">
      <i class="fas fa-key text-3xl text-orange-600 mr-3"></i>
      <h1 class="text-2xl font-bold text-[#4E342E]">Cambiar contraseña</h1>
    </div>

    <div class="bg-blue-100 border-l-4 border-blue-500 text-[#4E342E] p-4 mb-6 rounded">
      <p class="font-semibold mb-2">Consideraciones para la contraseña, debe contener:</p>
      <ul class="list-disc list-inside text-sm">
        <li>Al menos un número</li>
        <li>Al menos una mayúscula</li>
        <li>Al menos una minúscula</li>
        <li>Mínimo 8 caracteres</li>
        <li>Deberás iniciar sesión nuevamente</li>
      </ul>
    </div>

    <!-- Contraseña actual -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-lock mr-1"></i> Contraseña actual
      </label>
      <div class="relative">
        <input
          :type="showActual ? 'text' : 'password'"
          placeholder="Contraseña actual"
          v-model="password.actual"
          class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
        />
        <button type="button" class="absolute right-2 top-2 text-orange-600" @click="showActual = !showActual">
          <i :class="showActual ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
      </div>
    </div>

    <!-- Nueva contraseña -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-key mr-1"></i> Nueva contraseña
      </label>
      <div class="relative">
        <input
          :type="showNueva ? 'text' : 'password'"
          placeholder="Nueva contraseña"
          v-model="password.nueva"
          class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
        />
        <button type="button" class="absolute right-2 top-2 text-orange-600" @click="showNueva = !showNueva">
          <i :class="showNueva ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
      </div>
    </div>

    <!-- Repite la nueva contraseña -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-key mr-1"></i> Repite la nueva contraseña
      </label>
      <div class="relative">
        <input
          :type="showRepite ? 'text' : 'password'"
          placeholder="Repite la nueva contraseña"
          v-model="password.repiteNueva"
          class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
        />
        <button type="button" class="absolute right-2 top-2 text-orange-600" @click="showRepite = !showRepite">
          <i :class="showRepite ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
      </div>
    </div>

    <ul v-if="errores.length > 0" class="mb-4">
      <li class="text-red-600 text-center text-sm" v-for="error in errores" :key="error">{{ error }}</li>
    </ul>

    <div class="text-center">
      <button
        type="button"
        class="inline-flex items-center px-6 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg transition"
        @click="cambiarPassword"
        :disabled="cargando"
      >
        <i class="fas fa-check mr-2"></i> Cambiar contraseña
      </button>
    </div>
  </section>
</template>

<script>
import HttpService from '../../Servicios/HttpService'

export default {
  name: "CambiarPassword",

  data: () => ({
    password: {
      actual: "",
      nueva: "",
      repiteNueva: ""
    },
    errores: [],
    cargando: false,
    showActual: false,
    showNueva: false,
    showRepite: false
  }),

  methods: {
    async cambiarPassword() {
      let continuar = await this.validarDatos()
      if (continuar) {
        this.errores = []
        if (confirm('¿Cambiar contraseña? Deberás iniciar sesión de nuevo')) {
          this.cargando = true
          let id = localStorage.getItem('idUsuario')
          let payload = {
            id: id,
            password: this.password.repiteNueva
          }

          HttpService.registrar(payload, "cambiar_password.php").
            then(resultado => {
              if (resultado) {
                alert('Contraseña actualizada')
                this.cargando = false
                localStorage.removeItem('logeado')
                window.location.reload()
              }
            })
        }
      }
    },
    async verificarContraActual() {
      let id = localStorage.getItem('idUsuario')
      let payload = {
        password: this.password.actual,
        id: id
      }
      let resultado = await HttpService.obtenerConDatos(payload, "verificar_password.php")
      return resultado
    },

    async validarDatos() {
      let verificarSiCoincide = await this.verificarContraActual()
      this.errores = [];

      if (!this.password.actual) {
        this.errores.push("Debes colocar la contraseña actual.");
      }
      if (!verificarSiCoincide) {
        this.errores.push("La contraseña actual ingresada no es correcta.");
      }

      if (!this.password.nueva) {
        this.errores.push("Debes colocar la contraseña nueva.");
      } else if (!this.validPass(this.password.nueva)) {
        this.errores.push('La contraseña nueva debe ser válida.');
      }
      if (!this.password.repiteNueva) {
        this.errores.push("Debes repetir la contraseña nueva.");
      } else if (this.password.nueva != this.password.repiteNueva) {
        this.errores.push('Las contraseñas deben coincidir.');
      }

      if (!this.errores.length) {
        return true;
      }
    },

    validPass(password) {
      var re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/;
      return re.test(password);
    }
  }
}
</script>