<template>
  <b-modal :active.sync="visible" :width="500" trap-focus>
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg">
      <div class="flex items-center mb-4">
        <i class="fas fa-user text-2xl mr-3" style="color:#FF6F00"></i>
        <h2 class="text-xl font-bold" style="color:#4E342E;">
          {{ esEdicion ? 'Editar usuario' : 'Registrar usuario' }}
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
        <!-- Correo -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-envelope mr-1"></i> Correo electrónico
          </label>
          <input
            type="email"
            placeholder="Correo del usuario"
            v-model="usuarioLocal.correo"
            required
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
          />
        </div>
        <!-- Nombre -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-user-tag mr-1"></i> Nombre
          </label>
          <input
            type="text"
            placeholder="Nombre del usuario"
            v-model="usuarioLocal.nombre"
            required
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
            placeholder="Teléfono del usuario"
            v-model="usuarioLocal.telefono"
            required
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
          />
        </div>
        <!-- Rol -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-[#4E342E]">
            <i class="fas fa-user-shield mr-1"></i> Rol
          </label>
          <select
            v-model="usuarioLocal.rol_id"
            required
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1]"
          >
            <option value="" disabled>Selecciona un rol</option>
            <option v-for="rol in roles" :key="rol.id" :value="rol.id">
              {{ rol.nombre }}
            </option>
          </select>
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
            {{ cargando ? 'Guardando...' : (esEdicion ? 'Actualizar' : 'Registrar') }}
          </button>
        </div>
      </form>
    </div>
  </b-modal>
</template>

<script>
import Utiles from '../../Servicios/Utiles'
import HttpService from '../../Servicios/HttpService'

export default {
  name: "ModalUsuarios",
  props: {
    visible: { type: Boolean, required: true },
    usuario: { type: Object, default: () => ({}) },
    roles: { type: Array, default: () => [] },
    esEdicion: { type: Boolean, default: false }
  },
  data() {
    return {
      usuarioLocal: { ...this.usuario },
      errores: [],
      cargando: false
    }
  },
  watch: {
    usuario: {
      handler(val) {
        this.usuarioLocal = { ...val }
      },
      deep: true
    }
  },
  methods: {
    cerrar() {
      this.$emit("update:visible", false)
    },
    registrar() {
      let datos = {
        correo: this.usuarioLocal.correo,
        nombre: this.usuarioLocal.nombre,
        telefono: this.usuarioLocal.telefono,
        rol_id: this.usuarioLocal.rol_id
      }
      this.errores = Utiles.validar(datos)
      if (this.errores.length > 0) return

      this.cargando = true
      const ruta = this.esEdicion ? "editar_usuario.php" : "registrar_usuario.php"
      HttpService.registrar(this.usuarioLocal, ruta)
        .then(resultado => {
          this.cargando = false
          if (resultado) {
            this.$emit("registrado")
            this.$emit("update:visible", false)
          } else {
            this.errores = ["Ocurrió un error al guardar el usuario."]
          }
        })
        .catch(() => {
          this.cargando = false
          this.errores = ["Ocurrió un error al guardar el usuario."]
        })
    }
  }
}
</script>