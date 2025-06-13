<template>
  <section>
    <div id="comprobante" class="comprobante">
      <p class="comprobante-titulo">COMPROBANTE DE COMPRA</p>
      <strong class="comprobante-nombre">{{ datosLocal.nombre }}</strong><br />
      <img :src="logo" alt="logo" class="comprobante-logo" />
      <br>
      <span class="comprobante-info">Teléfono: {{ datosLocal.telefono }}</span><br />
      <p class="comprobante-fecha">Fecha: {{ venta.fecha }}</p>
      <p class="comprobante-atendio">Atiende: {{ venta.atendio }}</p>
      <p class="comprobante-cliente">Cliente: {{ venta.cliente }}</p>
      <table class="comprobante-tabla">
        <thead>
          <tr>
            <th colspan="2">Insumo</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(insumo, index) in insumos" :key="index">
            <td>{{ insumo.nombre }}</td>
            <td>${{ insumo.precio }} x {{ insumo.cantidad }}</td>
            <td>${{ parseFloat(insumo.cantidad * insumo.precio).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="comprobante-totales">
        <strong class="comprobante-total">TOTAL: ${{ venta.total }}</strong><br />
        <strong class="comprobante-pago">SU PAGO: ${{ venta.pagado }}</strong><br />
        <strong class="comprobante-cambio">CAMBIO: ${{ (venta.pagado - venta.total).toFixed(2) }}</strong>
      </div>
    </div>
  </section>
</template>

<script>
import Printd from "printd";

export default {
  name: "Ticket",
  props: ["venta", "insumos", "datosLocal", "logo"],

  data: () => ({
    cssText: `
      #comprobante {
        width: 300px;
        background: #fff8f1;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 18px 10px 10px 10px;
        text-align: center;
        font-family: 'Courier New', monospace;
        font-size: 10px;
        margin: 0 auto;
      }
      .comprobante-titulo {
        font-size: 13px;
        font-weight: bold;
        color: #FF6F00;
        letter-spacing: 1px;
        margin-bottom: 4px;
      }
      .comprobante-nombre {
        font-size: 14px;
        color: #4E342E;
        font-weight: bold;
      }
      .comprobante-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
        margin: 6px 0 8px 0;
        border-radius: 8px;
        border: 1px solid #ffe0b2;
        background: #fff;
      }
      .comprobante-info {
        font-size: 9px;
        color: #4E342E;
      }
      .comprobante-fecha,
      .comprobante-atendio,
      .comprobante-cliente {
        font-size: 10px;
        color: #4E342E;
        margin: 2px 0;
      }
      .comprobante-tabla {
        width: 100%;
        margin: 10px 0;
        border-collapse: collapse;
        font-size: 10px;
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
      }
      .comprobante-tabla th {
        background: #FF6F00;
        color: #fff;
        font-weight: bold;
        padding: 4px 2px;
        border: none;
        font-size: 10px;
      }
      .comprobante-tabla td {
        border-bottom: 1px solid #ffe0b2;
        padding: 3px 2px;
        color: #4E342E;
        font-size: 10px;
      }
      .comprobante-tabla tr:last-child td {
        border-bottom: none;
      }
      .comprobante-totales {
        margin-top: 10px;
        text-align: right;
      }
      .comprobante-total {
        color: #FF6F00;
        font-size: 13px;
        font-weight: bold;
      }
      .comprobante-pago {
        color: #4E342E;
        font-size: 12px;
        font-weight: bold;
      }
      .comprobante-cambio {
        color: #388e3c;
        font-size: 12px;
        font-weight: bold;
      }
    `,
  }),

  mounted() {
    this.d = new Printd();
    this.imprimir();
  },

  methods: {
    imprimir() {
      let zona = document.getElementById("comprobante");
      setTimeout(() => this.d.print(zona, [this.cssText]), 10);
      this.$emit("impreso", false);
    },
  },
};
</script>

<style scoped>
#comprobante {
  width: 300px;
  background: #fff8f1;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 18px 10px 10px 10px;
  text-align: center;
  font-family: 'Courier New', monospace;
  font-size: 10px;
  margin: 0 auto;
}
.comprobante-titulo {
  font-size: 13px;
  font-weight: bold;
  color: #FF6F00;
  letter-spacing: 1px;
  margin-bottom: 4px;
}
.comprobante-nombre {
  font-size: 14px;
  color: #4E342E;
  font-weight: bold;
}
.comprobante-logo {
  width: 70px;
  height: 70px;
  object-fit: contain;
  margin: 6px 0 8px 0;
  border-radius: 8px;
  border: 1px solid #ffe0b2;
  background: #fff;
}
.comprobante-info {
  font-size: 9px;
  color: #4E342E;
}
.comprobante-fecha,
.comprobante-atendio,
.comprobante-cliente {
  font-size: 10px;
  color: #4E342E;
  margin: 2px 0;
}
.comprobante-tabla {
  width: 100%;
  margin: 10px 0;
  border-collapse: collapse;
  font-size: 10px;
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
}
.comprobante-tabla th {
  background: #FF6F00;
  color: #fff;
  font-weight: bold;
  padding: 4px 2px;
  border: none;
  font-size: 10px;
}
.comprobante-tabla td {
  border-bottom: 1px solid #ffe0b2;
  padding: 3px 2px;
  color: #4E342E;
  font-size: 10px;
}
.comprobante-tabla tr:last-child td {
  border-bottom: none;
}
.comprobante-totales {
  margin-top: 10px;
  text-align: right;
}
.comprobante-total {
  color: #FF6F00;
  font-size: 13px;
  font-weight: bold;
}
.comprobante-pago {
  color: #4E342E;
  font-size: 12px;
  font-weight: bold;
}
.comprobante-cambio {
  color: #388e3c;
  font-size: 12px;
  font-weight: bold;
}
</style>