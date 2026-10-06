    import axios from "axios";

    export async function getExplorer(basePath = "", q = "") {

        const response = await axios.get("/api/buscador", {
            params: {
                basePath,
                q,
            },
        });

        console.log(response.data);

        return response.data.data;   // <-- SOLO el arreglo
    }
    export async function buscarGlobal(q) {
        const response = await fetch(
            `/api/buscador-global?q=${encodeURIComponent(q)}`
        );

        if (!response.ok) {
            throw new Error("Error en la búsqueda global");
        }

        const json = await response.json();

        return json.data || [];
    }