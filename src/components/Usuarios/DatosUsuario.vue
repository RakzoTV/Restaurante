<template>
  <section>
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
      <div class="text-center">
        <button
          type="submit"
          class="inline-flex items-center px-6 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg transition"
        >
          <i class="fas fa-check mr-2"></i> Registrar
        </button>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  name: "DatosUsuario",
  props: ["usuario", "roles"],

  data() {
    return {
      usuarioLocal: {
        correo: this.usuario.correo || "",
        nombre: this.usuario.nombre || "",
        telefono: this.usuario.telefono || "",
        rol_id: this.usuario.rol_id || ""
      },
      errores: []
    }
  },

  watch: {
    usuario: {
      handler(val) {
        this.usuarioLocal = {
          correo: val.correo || "",
          nombre: val.nombre || "",
          telefono: val.telefono || "",
          rol_id: val.rol_id || ""
        }
      },
      deep: true
    }
  },

  methods: {
    registrar() {
      this.errores = [];
      if (!this.usuarioLocal.correo) this.errores.push("El correo es obligatorio.");
      if (!this.usuarioLocal.nombre) this.errores.push("El nombre es obligatorio.");
      if (!this.usuarioLocal.telefono) this.errores.push("El teléfono es obligatorio.");
      if (!this.usuarioLocal.rol_id) this.errores.push("El rol es obligatorio.");
      if (this.errores.length > 0) return;

      // Emitir solo los datos planos
      this.$emit("registrado", { ...this.usuarioLocal });
    }
  }
}
</script>