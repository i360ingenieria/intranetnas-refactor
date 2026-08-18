export function toChonkyFiles(items) {
    return items.map(item => ({
        id: String(item.id),
        name: item.nombre,
        isDir: item.tipo === "carpeta",

        extraData: {
            ruta: item.ruta,
            tipo: item.tipo,
            extension: item.extension,
        },
    }));
}