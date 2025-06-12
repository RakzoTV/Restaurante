<template>
  <b-modal :active.sync="visible" :width="600" trap-focus>
    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="flex items-center mb-4">
        <i class="fas fa-utensils text-2xl mr-3" style="color:#FF6F00"></i>
        <h2 class="text-xl font-bold" style="color:#4E342E;">
          {{ esEdicion ? 'Editar Insumo' : 'Registrar Insumo' }}
        </h2>
        <button
          class="ml-auto text-[#4E342E] hover:text-red-600 transition"
          @click="$emit('update:visible', false)"
        >
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <datos-insumo
        :insumo="insumo"
        :es-edicion="esEdicion"
        @registrado="registrarYcerrar"
        @cancelar="$emit('update:visible', false)"
      />
    </div>
  </b-modal>
</template>

<script>
import DatosInsumo from "./DatosInsumo.vue";

export default {
  name: "ModalInsumo",
  components: { DatosInsumo },
  props: {
    visible: { type: Boolean, required: true },
    esEdicion: { type: Boolean, default: false },
    insumo: { type: Object, default: () => ({}) }
  },
  methods: {
    registrarYcerrar(data) {
      this.$emit("registrado", data);
      this.$emit("update:visible", false);
    }
  }
};
</script>