export function toChonkyFiles(items = []) {

    return items.map((item) => {

        const isDir =
            item.tipo === "carpeta";


        return {

            id: String(item.id),

            name: item.nombre,

            isDir: isDir,

            extraData: {

                ruta: item.ruta,

                ruta_relativa:
                    item.ruta_relativa,

                tipo:
                    item.tipo,

                extension:
                    item.extension || null,

            },

        };

    });

}