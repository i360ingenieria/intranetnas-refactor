const excelService = {
    async getWorkbook(fileId) {
        if (!fileId) {
            throw new Error("No se proporcionó el ID del archivo.");
        }

        const response = await fetch(
            `/excel/ver?id=${encodeURIComponent(fileId)}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                },
            }
        );

        let data = null;

        try {
            data = await response.json();
        } catch (error) {
            throw new Error(
                "El servidor no devolvió una respuesta JSON válida."
            );
        }

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