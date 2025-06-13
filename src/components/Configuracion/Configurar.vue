<template>
  <section class="min-h-screen bg-[#FFF8F1] p-6">
    <div class="flex items-center mb-8">
      <i class="fas fa-cogs text-3xl text-orange-600 mr-3"></i>
      <h1 class="text-3xl font-bold text-[#4E342E]">Configurar</h1>
    </div>
    <datos-configuracion
      @registrado="onRegistrado"
      :datos="datosLocal"
    ></datos-configuracion>
    <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
  </section>
</template>

<script>
import DatosConfiguracion from './DatosConfiguracion.vue'
import HttpService from '../../Servicios/HttpService'

export default {
  name: "Configurar",
  components: { DatosConfiguracion },

  data: () => ({
    cargando: false,
    datosLocal: {
      nombre: "",
      telefono: "",
      numeroMesas: 1,
    },
  }),

  mounted() {
    this.obtenerInformacion()
  },

  methods: {
    onRegistrado(datos) {
      this.cargando = true
      this.datosLocal = datos

      HttpService.registrar(this.datosLocal, "registrar_datos_local.php")
        .then(registrado => {
          if (registrado) {
            this.$buefy.toast.open({
              message: 'Información actualizada. Recarga la página para ver los cambios',
              type: 'is-success'
            })
            this.obtenerInformacion()
          }
        })
    },
    obtenerInformacion() {
      this.cargando = true
      HttpService.obtener("obtener_datos_local.php")
        .then(resultado => {
          this.datosLocal = resultado
          this.datosLocal.numeroMesas = parseInt(this.datosLocal.numeroMesas)
          this.cargando = false
        })
    }
  }
}
</script>