const RUTA_GLOBAL = "http://localhost:80/restaurante/api/"

const HttpService = {
    async registrar(datos, ruta) {
        const respuesta = await fetch(RUTA_GLOBAL + ruta, {
            method: "post",
            body: JSON.stringify(datos),
        });
        let resultado = await respuesta.json()
        return resultado
    },

    async actualizar(datos, ruta) {
        const respuesta = await fetch(RUTA_GLOBAL + ruta, {
            method: "post",
            body: JSON.stringify(datos),
        });
        let resultado = await respuesta.json()
        return resultado
    },

    async obtenerConDatos(datos, ruta) {
        const respuesta = await fetch(RUTA_GLOBAL + ruta, {
            method: "post",
            body: JSON.stringify(datos),
        });
        const text = await respuesta.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Respuesta no válida del servidor:", text);
            throw new Error("Respuesta no válida del servidor: " + text);
        }
    },


    async obtener(ruta) {
        let respuesta = await fetch(RUTA_GLOBAL + ruta)
        let datos = await respuesta.json()
        return datos
    },

    async eliminar(ruta, id) {
        const respuesta = await fetch(RUTA_GLOBAL + ruta, {
            method: "post",
            body: JSON.stringify(id),
        });
        let resultado = await respuesta.json()
        return resultado
    }


}



export default HttpService 