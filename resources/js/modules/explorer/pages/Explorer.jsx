 
import { useEffect, useMemo, useState } from "react";

import {
    FullFileBrowser,
    ChonkyActions,
} from "chonky";

import {
    getExplorer,
    buscarGlobal,
} from "../services/explorerService";

import { toChonkyFiles } from "../adapters/chonkyAdapter";
import { useExplorerStore } from "../store/explorerStore";

// ======================================================
// CARPETA RAÍZ DEL EXPLORADOR
// ======================================================

const ROOT_PATH =
    "/mnt/intranet/sistema gestion de calidad";

// ======================================================
// CONSTRUYE EL FOLDER CHAIN DE CHONKY
// ======================================================

function buildFolderChain(currentPath) {
    if (!currentPath) {
        currentPath = ROOT_PATH;
    }

    // Evitamos salir de la raíz
    if (
        currentPath !== ROOT_PATH &&
        !currentPath.startsWith(ROOT_PATH + "/")
    ) {
        currentPath = ROOT_PATH;
    }

    const relativePath =
        currentPath === ROOT_PATH
            ? ""
            : currentPath
                .replace(ROOT_PATH, "")
                .replace(/^\/+/, "");

    const parts =
        relativePath
            ? relativePath.split("/").filter(Boolean)
            : [];

    const chain = [
        {
            id: "root",
            name: "Sistema Gestión de Calidad",
            isDir: true,
            extraData: {
                ruta: ROOT_PATH,
                tipo: "carpeta",
            },
        },
    ];

    let accumulatedPath = ROOT_PATH;

    parts.forEach((part, index) => {
        accumulatedPath += "/" + part;

        chain.push({
            id: `folder-${index}-${accumulatedPath}`,
            name: part,
            isDir: true,
            extraData: {
                ruta: accumulatedPath,
                tipo: "carpeta",
            },
        });
    });

    return chain;
}

// ======================================================
// EXPLORER
// ======================================================

export default function Explorer() {

    const {
        basePath,
        files,
        setFiles,
        setBasePath,
    } = useExplorerStore();

    // ==================================================
    // ESTADO BÚSQUEDA GLOBAL
    // ==================================================

    const [busquedaGlobal, setBusquedaGlobal] = useState("");
    const [resultadosGlobales, setResultadosGlobales] = useState([]);
    const [buscandoGlobal, setBuscandoGlobal] = useState(false);

    // ==================================================
    // CARGAR ARCHIVOS CUANDO CAMBIA LA CARPETA
    // ==================================================

    useEffect(() => {
        loadFiles();
    }, [basePath]);

    // ==================================================
    // CARGAR ARCHIVOS DE LA CARPETA ACTUAL
    // ==================================================

    async function loadFiles() {
        try {
            const data = await getExplorer(
                basePath || ROOT_PATH
            );

            console.log("API:", data);

            const chonkyFiles =
                toChonkyFiles(data);

            console.log(
                "CHONKY:",
                chonkyFiles
            );

            setFiles(chonkyFiles);

        } catch (error) {

            console.error(
                "ERROR EXPLORER:",
                error
            );
        }
    }

    // ==================================================
    // BÚSQUEDA GLOBAL
    // ==================================================

    async function ejecutarBusquedaGlobal() {

        const termino =
            busquedaGlobal.trim();

        if (!termino) {
            setResultadosGlobales([]);
            return;
        }

        try {

            setBuscandoGlobal(true);

            const resultados =
                await buscarGlobal(termino);

            console.log(
                "RESULTADOS BÚSQUEDA GLOBAL:",
                resultados
            );

            setResultadosGlobales(
                resultados
            );

        } catch (error) {

            console.error(
                "ERROR BÚSQUEDA GLOBAL:",
                error
            );

            setResultadosGlobales([]);

        } finally {

            setBuscandoGlobal(false);
        }
    }

    // ==================================================
    // ENTER EN EL BUSCADOR
    // ==================================================

    function handleBusquedaKeyDown(event) {

        if (event.key === "Enter") {
            ejecutarBusquedaGlobal();
        }
    }

    // ==================================================
    // LIMPIAR BÚSQUEDA
    // ==================================================

    function limpiarBusqueda() {

        setBusquedaGlobal("");
        setResultadosGlobales([]);
    }

    // ==================================================
    // ABRIR RESULTADO DE BÚSQUEDA
    // ==================================================

    function abrirResultado(resultado) {

        console.log(
            "===== RESULTADO GLOBAL ====="
        );

        console.log(
            "RESULTADO:",
            resultado
        );

        // ------------------------------------------------
        // CARPETA
        // ------------------------------------------------

        if (
            resultado.tipo === "carpeta" ||
            resultado.tipo === "folder" ||
            resultado.tipo === "directory"
        ) {

            const ruta =
                resultado.ruta;

            if (!ruta) {

                console.error(
                    "La carpeta no tiene ruta:",
                    resultado
                );

                return;
            }

            console.log(
                "NAVEGANDO A CARPETA:",
                ruta
            );

            setBasePath(ruta);

            return;
        }

        // ------------------------------------------------
        // ARCHIVO
        // ------------------------------------------------

        const file = {
            id: resultado.id,
            name: resultado.nombre,
        };

        abrirArchivo(file);
    }

    // ==================================================
    // NAVEGACIÓN DE CARPETAS
    // ==================================================

    const folderChain = useMemo(() => {

        return buildFolderChain(
            basePath || ROOT_PATH
        );

    }, [basePath]);

    // ==================================================
    // ABRIR ARCHIVO
    // ==================================================

    function abrirArchivo(file) {

        const extension =
            file.name
                ?.split(".")
                .pop()
                ?.toLowerCase();

        console.log(
            "ABRIENDO ARCHIVO:",
            file.name
        );

        console.log(
            "ID:",
            file.id
        );

        console.log(
            "EXTENSION:",
            extension
        );

        // PDF
        if (extension === "pdf") {

            window.open(
                `/archivos/ver/${file.id}`,
                "_blank"
            );

            return;
        }

        // Excel
        if (
            extension === "xls" ||
            extension === "xlsx" ||
            //aca va la logica para descargar el xlsm, y .docx que no se puede abrir en el navegador
            extension === "xlsm"
        ) {

            window.open(
                `/archivos/excel  /${file.id}`,
                "_blank"
            );

            return;
        }

        // Otros archivos
        window.open(
            `/archivos/descargar/${file.id}`,
            "_blank"
        );
    }

    // ==================================================
    // ACCIONES CHONKY
    // ==================================================

    function handleAction(data) {

        console.log(
            "===== ACCIÓN CHONKY ====="
        );

        console.log(
            "ID:",
            data.id
        );

        console.log(
            "PAYLOAD:",
            data.payload
        );

        // ==================================================
        // ABRIR ARCHIVO / CARPETA
        // ==================================================

        if (
            data.id ===
            ChonkyActions.OpenFiles.id
        ) {

            const file =
                data.payload?.files?.[0];

            if (!file) {

                console.log(
                    "No hay archivo seleccionado"
                );

                return;
            }

            console.log(
                "SELECCIONADO:",
                file
            );

            // ----------------------------------------------
            // CARPETA
            // ----------------------------------------------

            if (file.isDir) {

                const ruta =
                    file.extraData?.ruta;

                console.log(
                    "CARPETA:",
                    file.name
                );

                console.log(
                    "RUTA:",
                    ruta
                );

                if (!ruta) {

                    console.error(
                        "La carpeta no tiene ruta:",
                        file
                    );

                    return;
                }

                setBasePath(ruta);

                return;
            }

            // ----------------------------------------------
            // ARCHIVO
            // ----------------------------------------------

            abrirArchivo(file);

            return;
        }

        // ==================================================
        // SUBIR UN NIVEL
        // ==================================================

        if (
            data.id ===
            ChonkyActions.OpenParentFolder.id
        ) {

            const actual =
                basePath || ROOT_PATH;

            if (actual === ROOT_PATH) {
                return;
            }

            const limpio =
                actual.replace(/\/$/, "");

            const posicion =
                limpio.lastIndexOf("/");

            const padre =
                limpio.substring(
                    0,
                    posicion
                );

            console.log(
                "SUBIENDO:",
                padre
            );

            setBasePath(
                padre || ROOT_PATH
            );

            return;
        }
    }

    // ==================================================
    // RENDER
    // ==================================================

    return (

        <div
            style={{
                width: "100%",
            }}
        >

            {/* ==================================================
                BÚSQUEDA GLOBAL
            ================================================== */}

            <div
                style={{
                    width: "100%",
                    marginBottom: "12px",
                    padding: "10px",
                    background: "#f8f9fa",
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                }}
            >

                <div
                    style={{
                        display: "flex",
                        gap: "8px",
                        alignItems: "center",
                    }}
                >

                    <input
                        type="text"
                        value={busquedaGlobal}
                        onChange={(event) =>
                            setBusquedaGlobal(
                                event.target.value
                            )
                        }
                        onKeyDown={
                            handleBusquedaKeyDown
                        }
                        placeholder="Buscar en todo el sistema..."
                        style={{
                            flex: 1,
                            height: "38px",
                            padding: "8px 12px",
                            border: "1px solid #ccc",
                            borderRadius: "4px",
                            fontSize: "14px",
                        }}
                    />

                    <button
                        type="button"
                        onClick={
                            ejecutarBusquedaGlobal
                        }
                        disabled={
                            buscandoGlobal
                        }
                        style={{
                            height: "38px",
                            padding: "0 16px",
                            border: "none",
                            borderRadius: "4px",
                            cursor: buscandoGlobal
                                ? "wait"
                                : "pointer",
                            background: "#007bff",
                            color: "#fff",
                            fontSize: "14px",
                        }}
                    >

                        {buscandoGlobal
                            ? "Buscando..."
                            : "Buscar"}

                    </button>

                    {resultadosGlobales.length > 0 && (

                        <button
                            type="button"
                            onClick={
                                limpiarBusqueda
                            }
                            style={{
                                height: "38px",
                                padding: "0 12px",
                                border: "1px solid #ccc",
                                borderRadius: "4px",
                                background: "#fff",
                                cursor: "pointer",
                                fontSize: "14px",
                            }}
                        >
                            Limpiar
                        </button>

                    )}

                </div>

                {/* ==================================================
                    RESULTADOS
                ================================================== */}

                {busquedaGlobal.trim() && (
                    <div
                        style={{
                            marginTop: "10px",
                        }}
                    >

                        {buscandoGlobal && (

                            <div
                                style={{
                                    padding: "10px",
                                    color: "#666",
                                    fontSize: "14px",
                                }}
                            >
                                Buscando resultados...
                            </div>

                        )}

                        {!buscandoGlobal &&
                            resultadosGlobales.length === 0 && (

                                <div
                                    style={{
                                        padding: "10px",
                                        color: "#666",
                                        fontSize: "14px",
                                    }}
                                >
                                    No se encontraron
                                    resultados para "
                                    {busquedaGlobal}
                                    ".
                                </div>

                            )}

                        {!buscandoGlobal &&
                            resultadosGlobales.length > 0 && (

                                <div
                                    style={{
                                        border: "1px solid #ddd",
                                        borderRadius: "4px",
                                        background: "#fff",
                                        maxHeight: "300px",
                                        overflowY: "auto",
                                    }}
                                >

                                    <div
                                        style={{
                                            padding: "8px 12px",
                                            borderBottom:
                                                "1px solid #ddd",
                                            fontWeight: "bold",
                                            fontSize: "14px",
                                        }}
                                    >
                                        Resultados encontrados:{" "}
                                        {
                                            resultadosGlobales.length
                                        }
                                    </div>

                                    {resultadosGlobales.map(
                                        (resultado) => {

                                            const esCarpeta =
                                                resultado.tipo ===
                                                    "carpeta" ||
                                                resultado.tipo ===
                                                    "folder" ||
                                                resultado.tipo ===
                                                    "directory";

                                            return (

                                                <div
                                                    key={`${resultado.tipo}-${resultado.id}`}
                                                    onClick={() =>
                                                        abrirResultado(
                                                            resultado
                                                        )
                                                    }
                                                    style={{
                                                        display: "flex",
                                                        alignItems:
                                                            "center",
                                                        gap: "10px",
                                                        padding:
                                                            "9px 12px",
                                                        borderBottom:
                                                            "1px solid #eee",
                                                        cursor: "pointer",
                                                    }}
                                                    onMouseEnter={(
                                                        event
                                                    ) => {
                                                        event.currentTarget.style.background =
                                                            "#f1f3f5";
                                                    }}
                                                    onMouseLeave={(
                                                        event
                                                    ) => {
                                                        event.currentTarget.style.background =
                                                            "#fff";
                                                    }}
                                                >

                                                    <span
                                                        style={{
                                                            fontSize:
                                                                "20px",
                                                            width: "28px",
                                                            textAlign:
                                                                "center",
                                                        }}
                                                    >
                                                        {esCarpeta
                                                            ? "📁"
                                                            : "📄"}
                                                    </span>

                                                    <div
                                                        style={{
                                                            minWidth: 0,
                                                            flex: 1,
                                                        }}
                                                    >

                                                        <div
                                                            style={{
                                                                fontWeight:
                                                                    "500",
                                                                fontSize:
                                                                    "14px",
                                                                overflow:
                                                                    "hidden",
                                                                textOverflow:
                                                                    "ellipsis",
                                                                whiteSpace:
                                                                    "nowrap",
                                                            }}
                                                        >
                                                            {
                                                                resultado.nombre
                                                            }
                                                        </div>

                                                        <div
                                                            style={{
                                                                marginTop:
                                                                    "3px",
                                                                color:
                                                                    "#777",
                                                                fontSize:
                                                                    "12px",
                                                                overflow:
                                                                    "hidden",
                                                                textOverflow:
                                                                    "ellipsis",
                                                                whiteSpace:
                                                                    "nowrap",
                                                            }}
                                                        >
                                                            {
                                                                resultado.ubicacion ||
                                                                resultado.ruta
                                                            }
                                                        </div>

                                                    </div>

                                                    <span
                                                        style={{
                                                            fontSize:
                                                                "11px",
                                                            color:
                                                                "#777",
                                                            whiteSpace:
                                                                "nowrap",
                                                        }}
                                                    >
                                                        {esCarpeta
                                                            ? "CARPETA"
                                                            : "ARCHIVO"}
                                                    </span>

                                                </div>

                                            );
                                        }
                                    )}

                                </div>

                            )}

                    </div>
                )}

            </div>

            {/* ==================================================
                EXPLORADOR CHONKY
            ================================================== */}

            <div
                style={{
                    height: "600px",
                    width: "100%",
                }}
            >

                <FullFileBrowser
                    files={files}
                    folderChain={folderChain}
                    onFileAction={handleAction}

                    /*
                     * No necesitamos arrastrar archivos.
                     * Esto evita el error:
                     *
                     * Cannot have two HTML5 backends
                     */

                    disableDragAndDrop={true}
                />

            </div>

        </div>
    );
}
 