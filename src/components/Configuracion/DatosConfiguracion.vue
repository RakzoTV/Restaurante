<template>
  <section>
    <!-- Nombre del local -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-store mr-1"></i> Nombre del local
      </label>
      <input
        type="text"
        placeholder="Nombre de tu local"
        v-model="datos.nombre"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
      />
    </div>

    <!-- Teléfono -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-phone mr-1"></i> Teléfono
      </label>
      <input
        type="text"
        placeholder="Número de teléfono"
        v-model="datos.telefono"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
      />
    </div>

    <!-- Número de mesas -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-chair mr-1"></i> Número de mesas
      </label>
      <input
        type="number"
        min="1"
        placeholder="Mesas"
        v-model.number="datos.numeroMesas"
        class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
      />
    </div>

    <!-- Logo -->
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-[#4E342E]">
        <i class="fas fa-image mr-1"></i> Logo
      </label>
      <div class="flex items-center gap-4">
        <label class="cursor-pointer inline-flex items-center px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold">
          <i class="fas fa-upload mr-2"></i> Seleccionar logo
          <input
            class="hidden"
            type="file"
            @input="pickFile"
            ref="fileInput"
            accept="image/*"
          />
        </label>
        <span v-if="previewImage" class="text-[#4E342E] text-sm">Logo seleccionado</span>
      </div>
    </div>

    <!-- Vista previa del logo -->
    <div id="preview" class="imagen mb-6">
      <img v-if="previewImage" :src="previewImage" class="w-[250px] h-[250px] object-contain rounded-xl border mx-auto" />
      <div v-else class="w-[250px] h-[250px] flex items-center justify-center bg-gray-100 rounded-xl border mx-auto text-gray-400">
        <i class="fas fa-image text-6xl"></i>
      </div>
    </div>

    <!-- Botón registrar -->
    <div class="text-center">
      <button
        type="button"
        class="inline-flex items-center px-6 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg transition"
        @click="registrar"
      >
        <i class="fas fa-check mr-2"></i> Registrar
      </button>
    </div>
  </section>
</template>

<script>
import Utiles from '../../Servicios/Utiles'

export default {
  name: "DatosConfiguracion",
  props: ["datos"],

  data: () => ({
    previewImage: null,
    cambiaImagen: false
  }),

  mounted() {
    this.previewImage = Utiles.generarUrlImagen(this.datos.logo)
  },

  methods: {
    registrar() {
      this.datos.logo = this.previewImage
      this.datos.cambiaImagen = this.cambiaImagen
      this.$emit("registrado", this.datos);
    },

    pickFile() {
      let input = this.$refs.fileInput
      let file = input.files
      if (file && file[0]) {
        let reader = new FileReader
        reader.onload = e => {
          this.previewImage = e.target.result
          this.cambiaImagen = true
        }
        reader.readAsDataURL(file[0])
        this.$emit('input', file[0])
      }
    },
  },

  watch: {
    '$props': {
      handler: function (val) {
        this.previewImage = Utiles.generarUrlImagen(val.datos.logo)
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.imagen {
  width: 250px;
  height: 250px;
  display: block;
  cursor: pointer;
  margin: 0 auto 30px;
  background-size: cover;
  background-position: center center;
}
</style>