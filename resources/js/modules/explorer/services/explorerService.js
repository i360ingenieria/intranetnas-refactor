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