<template>
  <section>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl shadow">
        <thead>
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Nombre</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Precio</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Cantidad</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Subtotal</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-[#4E342E] uppercase">Características</th>
            <th v-if="tipo === 'entregado'" class="px-4 py-2"></th>
            <th v-if="tipo === 'nuevo'" class="px-4 py-2 text-center text-xs font-semibold text-[#4E342E] uppercase">Quitar</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in lista" :key="item.id" class="hover:bg-[#FFF8F1]">
            <!-- Nombre -->
            <td class="px-4 py-2 text-[#4E342E]">{{ item.nombre }}</td>
            <!-- Precio -->
            <td class="px-4 py-2 text-[#4E342E]">${{ item.precio }}</td>
            <!-- Cantidad -->
            <td class="px-4 py-2 text-[#4E342E]">
              <span v-if="tipo === 'entregado'">{{ item.cantidad }}</span>
              <span v-if="tipo === 'nuevo'">
                <input
                  type="number"
                  min="1"
                  v-model.number="item.cantidad"
                  @input="calcularTotal"
                  class="w-20 border rounded px-2 py-1 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]"
                />
              </span>
            </td>
            <!-- Subtotal -->
            <td class="px-4 py-2 text-[#4E342E]">${{ item.cantidad * item.precio }}</td>
            <!-- Características -->
            <td class="px-4 py-2 text-[#4E342E]">
              <span v-if="tipo === 'entregado'">{{ item.caracteristicas }}</span>
              <span v-if="tipo === 'nuevo'">
                <input
                  type="text"
                  v-model="item.caracteristicas"
                  placeholder="Ej. Salsa roja, sin queso"
                  class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-[#FF6F00] text-[#4E342E] bg-[#FFF8F1]"
                />
              </span>
            </td>
            <!-- Estado (solo entregado) -->
            <td v-if="tipo === 'entregado'" class="px-4 py-2 text-center">
              <i
                v-if="item.estado === 'pendiente'"
                class="fas fa-exclamation-circle text-red-600"
                title="Pendiente"
              ></i>
              <i
                v-if="item.estado === 'entregado'"
                class="fas fa-check-circle text-green-600"
                title="Entregado"
              ></i>
            </td>
            <!-- Quitar (solo nuevo) -->
            <td v-if="tipo === 'nuevo'" class="px-4 py-2 text-center">
              <button
                class="inline-flex items-center px-3 py-1 rounded bg-red-600 hover:bg-red-800 text-white transition"
                @click="eliminar(item.id)"
                title="Quitar"
              >
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
export default {
  name: "ProductosOrden",
  props: ["lista", "tipo"],

  methods: {
    calcularTotal() {
      this.$emit("modificado");
    },

    eliminar(id) {
      this.$emit("quitar", id);
    },
  },
};
</script>