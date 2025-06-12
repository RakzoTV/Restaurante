<template>
    <section class="min-h-screen bg-[#FFF8F1] p-6">
        <!-- Tarjetas resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
            <div v-for="(carta, index) in cartas" :key="index"
                class="bg-white rounded-xl shadow-lg p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold" style="color: #4E342E;">{{ carta.encabezado }}</p>
                        <h4 class="text-lg font-bold" style="color: #4E342E;">{{ carta.titulo }}</h4>
                    </div>
                    <div>
                        <i :class="carta.icono" class="text-3xl" :style="{ color: carta.colorIcono }"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold" style="color: #FF6F00;">{{ carta.total }}</span>
                    <router-link :to="carta.ruta"
                        class="ml-2 px-3 py-1 rounded bg-[#FF6F00] hover:bg-[#E65100] text-white text-xs font-semibold transition">
                        <i class="fas fa-arrow-right"></i>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Paneles de ventas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-lg font-bold mb-2 flex items-center" style="color: #4E342E;">
                    <i class="far fa-clock mr-2"></i>
                    Ventas por hora
                    <span class="ml-auto bg-[#FF6F00] text-white px-3 py-1 rounded text-sm font-semibold">${{
                        totalVentasHora }}</span>
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: #4E342E;">Selecciona un periodo de
                        tiempo</label>
                    <b-datepicker placeholder="Click para seleccionar..." size="is-small" v-model="periodoHoras"
                        @input="busquedaAvanzada('hora')" range>
                    </b-datepicker>
                </div>
                <div id="contenedor-hora">
                    <canvas id="grafica-hora"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-lg font-bold mb-2 flex items-center" style="color: #4E342E;">
                    <i class="fas fa-user mr-2"></i>
                    Ventas de usuarios
                    <span class="ml-auto bg-[#FF6F00] text-white px-3 py-1 rounded text-sm font-semibold">${{
                        totalVentasUsuarios }}</span>
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: #4E342E;">Selecciona un periodo de
                        tiempo</label>
                    <b-datepicker placeholder="Click para seleccionar..." size="is-small" v-model="periodoUsuarios"
                        @input="busquedaAvanzada('usuario')" range>
                    </b-datepicker>
                </div>
                <div id="contenedor-usuarios">
                    <canvas id="grafica-usuarios"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-lg font-bold mb-2 flex items-center" style="color: #4E342E;">
                    <i class="fas fa-calendar-week mr-2"></i>
                    Ventas de la semana
                    <span class="ml-auto bg-[#FF6F00] text-white px-3 py-1 rounded text-sm font-semibold">${{
                        totalVentasSemana }}</span>
                </p>
                <div id="contenedor-semana">
                    <canvas id="grafica-semana"></canvas>
                </div>
            </div>
        </div>

        <!-- Panel ventas por año -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center mb-4">
                <i class="fas fa-calendar-alt mr-2 text-lg" style="color: #4E342E;"></i>
                <span class="text-lg font-bold" style="color: #4E342E;">Ventas por año</span>
                <span class="ml-auto bg-[#FF6F00] text-white px-3 py-1 rounded text-sm font-semibold">${{
                    totalVentasMeses }}</span>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: #4E342E;">Selecciona un año</label>
                <b-select size="is-small" v-model="anioSeleccionado" @change.native="busquedaAvanzada('mes')" expanded>
                    <option v-for="(anio, index) in listaAnios" :key="index" :value="anio">
                        {{ anio }}
                    </option>
                </b-select>
            </div>
            <div id="contenedor-mes">
                <canvas id="grafica-mes"></canvas>
            </div>
        </div>

        <!-- Insumos más vendidos y Mesas más ocupadas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-hamburger mr-2 text-lg" style="color: #4E342E;"></i>
                    <span class="text-lg font-bold" style="color: #4E342E;">Insumos más vendidos</span>
                    <div class="ml-auto">
                        <b-select v-model="limiteSeleccionado" @change.native="busquedaAvanzada('limite')"
                            size="is-small" expanded>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </b-select>
                    </div>
                </div>
                <!-- Tabla insumos más vendidos -->
                <b-table :data="insumosMasVendidos" :bordered="true" :striped="true">
                    <b-table-column field="icono" label="" v-slot="props">
                        <i v-if="props.row.tipo === 'PLATILLO'" class="fas fa-hamburger text-[#FF6F00]"></i>
                        <i v-if="props.row.tipo === 'BEBIDA'" class="fas fa-coffee text-[#4E342E]"></i>
                    </b-table-column>
                    <b-table-column field="nombre" label="Nombre" v-slot="props">
                        {{ props.row.nombre }}
                    </b-table-column>
                    <b-table-column field="categoria" label="Categoria" v-slot="props">
                        {{ props.row.categoria }}
                    </b-table-column>
                    <b-table-column field="total" label="Total" v-slot="props">
                        ${{ props.row.total }}
                    </b-table-column>
                    <b-table-column field="progreso" label="Progreso" v-slot="props">
                        <b-progress :value="props.row.progreso" show-value format="percent" :type="{
                            'is-success': props.row.progreso >= 90,
                            'is-info': props.row.progreso >= 70 && props.row.progreso < 90,
                            'is-danger': props.row.progreso < 70
                        }"></b-progress>
                    </b-table-column>
                </b-table>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-chair mr-2 text-lg" style="color: #4E342E;"></i>
                    <span class="text-lg font-bold" style="color: #4E342E;">Mesas más ocupadas</span>
                </div>
                <b-table :data="totalesPorMesa" :bordered="true" :striped="true">
                    <b-table-column field="idMesa" label="Mesa" v-slot="props">
                        Mesa #{{ props.row.idMesa }}
                    </b-table-column>
                    <b-table-column field="total" label="Total" v-slot="props">
                        ${{ props.row.total }}
                    </b-table-column>
                    <b-table-column field="progreso" label="Progreso" v-slot="props">
                        <b-progress :value="props.row.progreso" show-value format="percent" :type="{
                            'is-success': props.row.progreso >= 90,
                            'is-info': props.row.progreso >= 70 && props.row.progreso < 90,
                            'is-danger': props.row.progreso < 70
                        }"></b-progress>
                    </b-table-column>
                </b-table>
            </div>
        </div>
        <b-loading :is-full-page="true" v-model="cargando" :can-cancel="false"></b-loading>
    </section>
</template>
<script>
import HttpService from '../Servicios/HttpService'
import Utiles from '../Servicios/Utiles'

export default {
    name: "Inicio",

    data: () => ({
        ventasSemana: [],
        ventasHora: [],
        ventasMeses: [],
        ventasUsuarios: [],
        insumosMasVendidos: [],
        totalesPorMesa: [],
        resultadoCartas: {},
        cargando: false,
        tipoGrafica: "line",
        cartas: [],
        totalVentasHora: 0,
        totalVentasSemana: 0,
        totalVentasUsuarios: 0,
        totalVentasMeses: 0,
        periodoHoras: [],
        periodoUsuarios: [],
        anioSeleccionado: new Date().getFullYear(),
        listaAnios: [],
        limiteSeleccionado: 5,
        filtros: {
            hora: { inicio: "", fin: "" },
            usuarios: { inicio: "", fin: "" }
        }
    }),

    mounted() {
        this.filtros.anio = this.anioSeleccionado
        this.filtros.limite = this.limiteSeleccionado
        this.obtenerDatos()
        this.llenarListaAnios()
    },

    methods: {

        calcularProgreso(arreglo) {
            let mayor = (arreglo[0]) ? arreglo[0].total : 0
            arreglo.forEach(elemento => {
                elemento.progreso = parseInt(elemento.total * 100 / mayor)
            });
            return arreglo
        },

        busquedaAvanzada(tipo) {
            switch (tipo) {
                case "hora":
                    this.filtros.hora = {
                        inicio: this.periodoHoras[0] ? this.periodoHoras[0].toISOString().substring(0, 10) : "",
                        fin: this.periodoHoras[1] ? this.periodoHoras[1].toISOString().substring(0, 10) : ""
                    }
                    break
                case "usuario":
                    this.filtros.usuarios = {
                        inicio: this.periodoUsuarios[0] ? this.periodoUsuarios[0].toISOString().substring(0, 10) : "",
                        fin: this.periodoUsuarios[1] ? this.periodoUsuarios[1].toISOString().substring(0, 10) : ""
                    }
                    break
                case "mes":
                    this.filtros.anio = this.anioSeleccionado
                    break
                case "limite":
                    this.filtros.limite = this.limiteSeleccionado
            }
            this.obtenerDatos()
        },

        llenarListaAnios() {
            this.listaAnios = []
            for (let i = 2015; i <= this.anioSeleccionado; i++) {
                this.listaAnios.push(i)
            }
        },

        obtenerDatos() {
            this.cargando = true
            HttpService.obtenerConDatos(this.filtros, "inicio.php")
                .then(resultado => {
                    this.ventasSemana = Utiles.cambiarDiaSemana(resultado.ventasDiasSemana)
                    this.ventasHora = resultado.ventasHora
                    this.ventasMeses = Utiles.cambiarNumeroANombreMes(resultado.ventasMeses)
                    this.ventasUsuarios = resultado.ventasUsuario
                    this.resultadoCartas = resultado.cartas
                    this.insumosMasVendidos = this.calcularProgreso(resultado.insumosMasVendidos)
                    this.totalesPorMesa = this.calcularProgreso(resultado.totalesPorMesa)

                    this.totalVentasHora = Utiles.calcularTotales(this.ventasHora)
                    this.totalVentasSemana = Utiles.calcularTotales(this.ventasSemana)
                    this.totalVentasUsuarios = Utiles.calcularTotales(this.ventasUsuarios)
                    this.totalVentasMeses = Utiles.calcularTotales(this.ventasMeses)

                    Utiles.generarGrafica(this.ventasSemana, "#contenedor-semana", "#grafica-semana", "grafica-semana")
                    Utiles.generarGrafica(this.ventasHora, "#contenedor-hora", "#grafica-hora", "grafica-hora")
                    Utiles.generarGrafica(this.ventasMeses, "#contenedor-mes", "#grafica-mes", "grafica-mes")
                    Utiles.generarGrafica(this.ventasUsuarios, "#contenedor-usuarios", "#grafica-usuarios", "grafica-usuarios")

                    // Cartas con Font Awesome y colores personalizados
                    this.cartas = [
                        {
                            encabezado: "Ventas del día",
                            titulo: "Ventas hoy",
                            total: "$" + (this.resultadoCartas.totalVentasDia || 0),
                            icono: "fas fa-cash-register",
                            colorIcono: "#FF6F00",
                            ruta: "/reporte-ventas"
                        },
                        {
                            encabezado: "Estado mesas",
                            titulo: "Mesas ocupadas",
                            total: this.resultadoCartas.numeroMesasOcupadas || 0,
                            icono: "fas fa-chair",
                            colorIcono: "#4E342E",
                            ruta: "/realizar-orden"
                        },
                        {
                            encabezado: "Usuarios registrados",
                            titulo: "Usuarios",
                            total: this.resultadoCartas.numeroUsuarios || 0,
                            icono: "fas fa-users",
                            colorIcono: "#FF6F00",
                            ruta: "/usuarios"
                        },
                        {
                            encabezado: "Insumos registrados",
                            titulo: "Insumos",
                            total: this.resultadoCartas.numeroInsumos || 0,
                            icono: "fas fa-hamburger",
                            colorIcono: "#4E342E",
                            ruta: "/insumos"
                        },
                        {
                            encabezado: "Total ventas",
                            titulo: "Ventas",
                            total: "$" + (this.resultadoCartas.totalVentas || 0),
                            icono: "fas fa-cash-register",
                            colorIcono: "#FF6F00",
                            ruta: "/reporte-ventas"
                        },
                    ]
                    this.cargando = false
                })
        },
    }
}
</script>