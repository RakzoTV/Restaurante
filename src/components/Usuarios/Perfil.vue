<template>
  <div>
    <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>

    <section class="bg-cover bg-center rounded-xl mb-8 p-6" style="background-image: url('../../assets/fondo.png');">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center mb-4 md:mb-0">
          <img src="../../assets/usuario.png" alt="" class="w-24 h-24 rounded-full border-4 border-orange-600 shadow" />
          <div class="ml-6">
            <h1 class="text-3xl font-bold text-[#4E342E]">Perfil de {{ nombreUsuario }}</h1>
          </div>
        </div>
        <router-link
          to="/cambiar-password"
          class="inline-flex items-center px-6 py-3 rounded bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg transition"
        >
          <i class="fas fa-lock mr-2"></i> Cambiar contraseña
        </router-link>
      </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Ventas semana -->
      <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col">
        <div class="flex items-center mb-2">
          <i class="fas fa-calendar-week text-2xl mr-2 text-orange-600"></i>
          <span class="text-lg font-bold text-[#4E342E]">Tus ventas de la semana</span>
          <span class="ml-auto bg-orange-600 text-white text-lg font-bold rounded px-4 py-1 shadow">
            ${{ totalVentasSemana }}
          </span>
        </div>
        <div id="contenedor-semana">
          <canvas id="grafica-semana"></canvas>
        </div>
      </div>
      <!-- Ventas mes -->
      <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col">
        <div class="flex items-center mb-2">
          <i class="fas fa-calendar-alt text-2xl mr-2 text-orange-600"></i>
          <span class="text-lg font-bold text-[#4E342E]">Tus ventas del mes</span>
          <span class="ml-auto bg-orange-600 text-white text-lg font-bold rounded px-4 py-1 shadow">
            ${{ totalVentasMes }}
          </span>
        </div>
        <div class="flex gap-2 mb-2">
          <select
            v-model="mesSeleccionado"
            @change="busquedaAvanzada('mes')"
            class="border rounded px-2 py-1 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1] text-sm"
          >
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
          <select
            v-model="anioSeleccionado"
            @change="busquedaAvanzada('anioMes')"
            class="border rounded px-2 py-1 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1] text-sm"
          >
            <option v-for="anio in listaAnios" :key="anio" :value="anio">
              {{ anio }}
            </option>
          </select>
        </div>
        <div id="contenedor-mes">
          <canvas id="grafica-mes"></canvas>
        </div>
      </div>
      <!-- Ventas año -->
      <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col">
        <div class="flex items-center mb-2">
          <i class="fas fa-calendar text-2xl mr-2 text-orange-600"></i>
          <span class="text-lg font-bold text-[#4E342E]">Tus ventas del año</span>
          <span class="ml-auto bg-orange-600 text-white text-lg font-bold rounded px-4 py-1 shadow">
            ${{ totalVentasAnio }}
          </span>
        </div>
        <div class="mb-2">
          <select
            v-model="anioSeleccionadoGrafica"
            @change="busquedaAvanzada('anio')"
            class="border rounded px-2 py-1 focus:ring-2 focus:ring-orange-600 text-[#4E342E] bg-[#FFF8F1] text-sm"
          >
            <option v-for="anio in listaAnios" :key="anio" :value="anio">
              {{ anio }}
            </option>
          </select>
        </div>
        <div id="contenedor-anio">
          <canvas id="grafica-anio"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HttpService from "../../Servicios/HttpService";
import Utiles from "../../Servicios/Utiles";
export default {
  name: "Perfil",

  data: () => ({
    nombreUsuario: "",
    ventasSemana: [],
    totalVentasSemana: 0,
    ventasMes: [],
    totalVentasMes: 0,
    ventasAnio: [],
    totalVentasAnio: 0,
    filtros: {
      porMes: {},
    },
    mesSeleccionado: new Date().getMonth() + 1,
    anioSeleccionado: new Date().getFullYear(),
    anioSeleccionadoGrafica: new Date().getFullYear(),
    listaAnios: [],
    cargando: false
  }),

  mounted() {
    this.nombreUsuario = localStorage.getItem("nombreUsuario");
    this.filtros.idUsuario = localStorage.getItem("idUsuario");
    this.filtros.porMes.mes = this.mesSeleccionado;
    this.filtros.porMes.anio = this.anioSeleccionado;
    this.filtros.anioSeleccionadoGrafica = this.anioSeleccionadoGrafica;
    this.obtenerDatos();
    this.llenarListaAnios();
  },

  methods: {
    llenarListaAnios() {
      for (let i = 2015; i <= this.anioSeleccionado; i++) {
        this.listaAnios.push(i);
      }
    },

    busquedaAvanzada(tipo) {
      switch (tipo) {
        case "mes":
          this.filtros.porMes.mes = this.mesSeleccionado
          break;

        case "anioMes":
          this.filtros.porMes.anio = this.anioSeleccionado
          break;

        case "anio":
          this.filtros.anioSeleccionadoGrafica = this.anioSeleccionadoGrafica
          break;
      }

      this.obtenerDatos();
    },

    obtenerDatos() {
      this.cargando = true
      HttpService.obtenerConDatos(this.filtros, "perfil.php").then(
        (resultado) => {
          this.ventasSemana = Utiles.cambiarDiaSemana(resultado.ventasSemana);
          this.totalVentasSemana = Utiles.calcularTotales(this.ventasSemana);
          this.ventasMes = resultado.ventasMes;
          this.totalVentasMes = Utiles.calcularTotales(this.ventasMes);
          this.ventasAnio = Utiles.cambiarNumeroANombreMes(
            resultado.ventasAnio
          );
          this.totalVentasAnio = Utiles.calcularTotales(this.ventasAnio);

          Utiles.generarGrafica(
            this.ventasSemana,
            "#contenedor-semana",
            "#grafica-semana",
            "grafica-semana"
          );
          Utiles.generarGrafica(
            this.ventasMes,
            "#contenedor-mes",
            "#grafica-mes",
            "grafica-mes"
          );
          Utiles.generarGrafica(
            this.ventasAnio,
            "#contenedor-anio",
            "#grafica-anio",
            "grafica-anio"
          );

          this.cargando = false
        }
      );
    },
  },
};
</script>