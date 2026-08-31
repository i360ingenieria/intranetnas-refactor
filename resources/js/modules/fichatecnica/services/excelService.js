const excelService = {
    async getWorkbook(fileId) {
     if (!fileId) {
throw new Error("No se proporcionó el ID del archivo.");
}

    const url = `/excel/ver?id=${encodeURIComponent(fileId)}`;

    console.log("EXCEL - ID recibido:", fileId);
    console.log("EXCEL - URL solicitada:", url);

    const response = await fetch(url, {
        method: "GET",
        headers: {
            Accept: "application/json",
        },
    });

    console.log("EXCEL - HTTP status:", response.status);
    console.log("EXCEL - Content-Type:", response.headers.get("content-type"));

    /*
     * Primero obtenemos el texto crudo.
     * Esto nos permite saber exactamente qué está devolviendo Laravel.
     */
    const raw = await response.text();

    console.log("EXCEL - RESPUESTA RAW:", raw.substring(0, 2000));

    let data = null;

    try {
        data = JSON.parse(raw);
    } catch (error) {
        console.error("EXCEL - ERROR JSON:", error);
        console.error("EXCEL - RESPUESTA COMPLETA:", raw);

        throw new Error(
            `El servidor no devolvió una respuesta JSON válida. HTTP ${response.status}`
        );
    }

    console.log("EXCEL - JSON recibido:", data);

    if (!response.ok) {
        throw new Error(
            data?.message ||
            data?.error ||
            `Error HTTP ${response.status}`
        );
    }

    if (data?.error) {
        throw new Error(data.error);
    }

    return data;
},

};

export default excelService;