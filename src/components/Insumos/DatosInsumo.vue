<template>
    <section>
        <ul v-if="errores.length > 0">
            <li class="has-text-danger has-text-centered" v-for="error in errores" :key="error">{{ error }}</li>
        </ul>
        <b-field label="Tipo">
            <b-select v-model="localInsumo.tipo" @change.native="obtenerCategorias">
                <option value="" disabled>Selecciona el tipo de insumo</option>
                <option value="PLATILLO">Platillo</option>
                <option value="BEBIDA">Bebida</option>
            </b-select>
        </b-field>

        <b-field label="Código">
            <b-input type="text" placeholder="Código identificador del insumo" v-model="localInsumo.codigo"></b-input>
        </b-field>

        <b-field label="Nombre">
            <b-input type="text" placeholder="Nombre del insumo" v-model="localInsumo.nombre"></b-input>
        </b-field>

        <b-field label="Descripción">
            <b-input maxlength="200" type="textarea" placeholder="Escribe una pequeña descripción del insumo"
                v-model="localInsumo.descripcion"></b-input>
        </b-field>

        <b-field label="Categoría">
            <b-select v-model="localInsumo.categoria">
                <option value="" disabled>Selecciona la categoría</option>
                <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                    {{ categoria.nombre }}
                </option>
            </b-select>
        </b-field>

        <b-field label="Precio">
            <b-input type="number" placeholder="Precio de venta del insumo" v-model="localInsumo.precio"></b-input>
        </b-field>
        <div class="has-text-centered">
            <b-button :type="esEdicion ? 'is-info' : 'is-success'" size="is-large"
                :icon-left="esEdicion ? 'content-save' : 'check'" @click="registrar">
                {{ esEdicion ? 'Guardar cambios' : 'Registrar' }}
            </b-button>
            <b-button type="is-danger" size="is-large" icon-left="close" class="ml-2" @click="$emit('cancelar')">
                Cancelar
            </b-button>
        </div>
    </section>
</template>
<script>
import Utiles from '../../Servicios/Utiles'
import HttpService from '../../Servicios/HttpService'

export default {
    name: "DatosInsumo",
    props: {
        insumo: { type: Object, required: true },
        esEdicion: { type: Boolean, default: false }
    },

    data() {
        return {
            errores: [],
            categorias: [],
            localInsumo: this.copiarInsumo(this.insumo)
        }
    },

    watch: {
        insumo: {
            handler(newVal) {
                this.localInsumo = this.copiarInsumo(newVal)
                if (this.localInsumo.tipo) {
                    this.obtenerCategorias()
                }
            },
            deep: true,
            immediate: true
        }
    },

    methods: {
        copiarInsumo(obj) {
            // Copia profunda para evitar problemas de referencia
            return JSON.parse(JSON.stringify(obj || {
                tipo: "",
                codigo: "",
                nombre: "",
                descripcion: "",
                categoria: "",
                precio: ""
            }))
        },

        registrar() {
            console.log("Click registrar");
            let datos = {
                tipo: this.localInsumo.tipo,
                codigo: this.localInsumo.codigo,
                nombre: this.localInsumo.nombre,
                descripcion: this.localInsumo.descripcion,
                categoria: this.localInsumo.categoria,
                precio: this.localInsumo.precio
            }
            this.errores = Utiles.validar(datos)
            if (this.errores.length > 0) return
            console.log("Emitiendo evento registrado", this.localInsumo);
            this.$emit("registrado", { ...this.localInsumo })
            if (!this.esEdicion) {
                this.localInsumo = this.copiarInsumo({})
            }
        },

        obtenerCategorias() {
            if (!this.localInsumo.tipo) {
                this.categorias = []
                this.localInsumo.categoria = ""
                return
            }
            HttpService.obtenerConDatos(this.localInsumo.tipo, "obtener_categorias_tipo.php")
                .then(resultado => {
                    this.categorias = resultado || []
                    const existe = this.categorias.some(cat => cat.id == this.localInsumo.categoria)
                    if (!existe) {
                        this.localInsumo.categoria = ""
                    }
                })
        }
    }
}
</script>