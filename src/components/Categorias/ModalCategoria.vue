<template>
  <b-modal :active.sync="visible" :width="600" trap-focus>
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg">
      <div class="flex items-center mb-4">
        <i class="fas fa-archive text-2xl mr-3" style="color:#FF6F00"></i>
        <h2 class="text-xl font-bold" style="color:#4E342E;">
          {{ titulo }} Categoría
        </h2>
        <button
          class="ml-auto text-[#4E342E] hover:text-red-600 transition"
          @click="cerrar"
          type="button"
        >
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <form @submit.prevent="registrar">
        <ul v-if="errores.length > 0" class="mb-4">
          <li class="text-red-600 text-sm" v-for="error in errores" :key="error">{{ error }}</li>
        </ul>
        <!-- Tipo -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-list mr-1"></i> Tipo
          </label>
          <select
            required
            v-model="categoria.tipo"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]"
          >
            <option value="" disabled>Selecciona</option>
            <option value="PLATILLO">Platillo</option>
            <option value="BEBIDA">Bebida</option>
          </select>
        </div>
        <!-- Nombre -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-tag mr-1"></i> Nombre
          </label>
          <input
            type="text"
            placeholder="Nombre de la categoría"
            v-model="categoria.nombre"
            required
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]"
          />
        </div>
        <!-- Descripción -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-align-left mr-1"></i> Descripción
          </label>
          <textarea
            maxlength="200"
            v-model="categoria.descripcion"
            required
            placeholder="Pequeña descripción de la categoría"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]"
          ></textarea>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button
            type="button"
            class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-[#4E342E] font-semibold"
            @click="cerrar"
          >
            Cerrar
          </button>
          <button
            type="submit"
            class="px-4 py-2 rounded bg-orange-600 hover:bg-orange-700 text-white font-semibold flex items-center"
            :disabled="cargando"
          >
            <i class="fas fa-save mr-2"></i>
            {{ cargando ? 'Guardando...' : (tipo === 'registra' ? 'Registrar' : 'Actualizar') }}
          </button>
        </div>
      </form>
    </div>
  </b-modal>
</template>

<script>
import HttpService from '../../Servicios/HttpService'
import Utiles from '../../Servicios/Utiles'
export default {
  name: "ModalCategoria",
  props: {
    visible: { type: Boolean, required: true },
    categoria: { type: Object, required: true },
    titulo: { type: String, required: true },
    tipo: { type: String, required: true }
  },

  data: () => ({
    cargando: false,
    errores: []
  }),

  methods: {
    cerrar() {
      this.$emit("registrado", false)
      this.$emit("update:visible", false)
    },

    registrar() {
      let datos = {
        tipo: this.categoria.tipo,
        nombre: this.categoria.nombre,
        descripcion: this.categoria.descripcion
      }
      this.errores = Utiles.validar(datos)
      if (this.errores.length > 0) return

      let ruta = (this.tipo === "registra") ? "registrar_categoria.php" : "editar_categoria.php"
      this.cargando = true
      HttpService.registrar(this.categoria, ruta)
        .then(registrado => {
          if (registrado) {
            this.cargando = false
            this.$emit("registrado", true)
            this.$emit("update:visible", false)
          }
        })
    }
  }
}
</script>