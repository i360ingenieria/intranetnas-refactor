import { useEffect, useMemo, useState } from "react";

import {
    FullFileBrowser,
    ChonkyActions,
} from "chonky";

import { getFichaTecnica, buscarGlobalf } from "../services/fichatecnicaService";
import { toChonkyFiles } from "../adapter/fichatecnicaAdapter";
import { useFichaTecnicaStore } from "../store/fichatecnicaStore";


// =====================================================
// RAÍZ DEL NAS DE FICHA TÉCNICA
// =====================================================

const ROOT_PATH = "/mnt/nas_pcmercadeo/";


// =====================================================
// CONSTRUIR FOLDER CHAIN
// =====================================================

function buildFolderChain(currentPath) {

    let path = currentPath || ROOT_PATH;

    // Normalizar raíz
    if (
        path !== ROOT_PATH &&
        path !== ROOT_PATH.replace(/\/$/, "")
    ) {
        if (!path.startsWith(ROOT_PATH)) {
            path = ROOT_PATH;
        }
    }

    const normalizedRoot = ROOT_PATH.replace(/\/$/, "");

    const normalizedPath =
        path.replace(/\/$/, "") || normalizedRoot;

    // Obtener ruta relativa
    let relativePath = "";

    if (normalizedPath !== normalizedRoot) {

        relativePath = normalizedPath
            .replace(normalizedRoot, "")
            .replace(/^\/+/, "");

    }

    const parts = relativePath
        ? relativePath.split("/").filter(Boolean)
        : [];


    // =================================================
    // ROOT
    // =================================================

    const chain = [
        {
            id: "ficha-root",
            name: "Ficha Técnica",
            isDir: true,

            extraData: {
                ruta: ROOT_PATH,
                tipo: "carpeta",
            },
        },
    ];


    // =================================================
    // CARPETAS
    // =================================================

    let accumulatedPath = normalizedRoot;

    parts.forEach((part, index) => {

        accumulatedPath += "/" + part;

        chain.push({
            id: `ficha-folder-${index}-${accumulatedPath}`,

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


// =====================================================
// COMPONENTE
// =====================================================

export default function FichaTecnica() {

    const {
        basePath,
        files,
        setFiles,
        setBasePath,
    } = useFichaTecnicaStore();

 // ==================================================
    // ESTADO BÚSQUEDA GLOBAL
    // ==================================================
     const [busquedaGlobalf, setBusquedaGlobalF] = useState("");
     const [resultadosGlobalesF, setResultadosGlobalesF] = useState([]);
     const [buscandoGlobalF, setBuscandoGlobalF] = useState(false);
     
    // =================================================
    // RUTA ACTUAL
    // =================================================

    const currentPath =
        basePath || ROOT_PATH;


    // =================================================
    // CARGAR CONTENIDO
    // =================================================

    useEffect(() => {
       
        let cancelled = false;

          
        async function loadFiles() {

            try {

                console.log(
                    "=================loadfiles================"
                );

                console.log(
                    "FICHA TECNICA - CARGANDO"
                );

                console.log(
                    "RUTA:",
                    currentPath
                );


                const data =
                    await getFichaTecnica(currentPath);


                console.log(
                    "FICHA API:",
                    data
                );


                const chonkyFiles =
                    toChonkyFiles(data);


                console.log(
                    "FICHA CHONKY:",
                    chonkyFiles
                );


                if (!cancelled) {

                    setFiles(
                        chonkyFiles
                    );

                }

            } catch (error) {

                console.error(
                    "ERROR FICHA TECNICA:",
                    error
                );


                if (!cancelled) {

                    setFiles([]);

                }

            }

        }


        loadFiles();


        return () => {

            cancelled = true;

        };

    }, [
        currentPath,
        setFiles,
    ]);
    
    // ==================================================
    // BÚSQUEDA GLOBAL
    // ==================================================
     async function ejecutarBusquedaGlobal() {
       
        const termino = busquedaGlobalf.trim();
        if (!termino) {
            setResultadosGlobalesF([]);
            return;
        }

       
        try {
            setBuscandoGlobalF(true);

            const resultados =
                  await buscarGlobalf(termino);
                  console.log("RESULTADOS BÚSQUEDA GLOBAL:", resultados);

            setResultadosGlobalesF(
                resultados
            );
        } catch (error) {
            console.error("Error en la búsqueda global:", error);
            setResultadosGlobalesF([]);
        } finally {
            setBuscandoGlobalF(false);
        }    
        //setBuscandoGlobalF.trim();

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

        setBusquedaGlobalF("");
        setResultadosGlobalesF([]);
    }

    
    // =================================================
    // FOLDER CHAIN
    // =================================================

    const folderChain = useMemo(() => {

        return buildFolderChain(
            currentPath
        );

    }, [
        currentPath,
    ]);


    // =================================================
    // IR A CARPETA
    // =================================================

    function openFolder(file) {

        const ruta =
            file?.extraData?.ruta;


        if (!ruta) {

            console.error(
                "CARPETA SIN RUTA:",
                file
            );

            return;

        }


        console.log(
            "ENTRANDO A CARPETA:",
            ruta
        );


        setBasePath(
            ruta
        );

    }


    // =================================================
    // SUBIR UN NIVEL
    // =================================================

    function goUp() {

        const root =
            ROOT_PATH.replace(/\/$/, "");


        const actual =
            (
                currentPath ||
                ROOT_PATH
            ).replace(/\/$/, "");


        // Ya estamos en raíz
        if (
            actual === root
        ) {

            console.log(
                "FICHA: ya estamos en la raíz"
            );

            return;

        }


        const posicion =
            actual.lastIndexOf("/");


        if (posicion <= root.length) {

            setBasePath(
                ROOT_PATH
            );

            return;

        }


        const padre =
            actual.substring(
                0,
                posicion
            );


        console.log(
            "FICHA: SUBIENDO A:",
            padre
        );


        setBasePath(
            padre
        );

    }


    // =================================================
    // ABRIR ARCHIVO
    // =================================================

    function openFile(file) {

        if (!file) {

            return;

        }


        const extension =
            (
                file.extraData?.extension ||
                file.name
                    ?.split(".")
                    .pop() ||
                ""
            ).toLowerCase();


        console.log(
            "================================="
        );

        console.log(
            "FICHA: ABRIENDO ARCHIVO"
        );

        console.log(
            "ID:",
            file.id
        );

        console.log(
            "NOMBRE:",
            file.name
        );

        console.log(
            "EXTENSION:",
            extension
        );


        // =================================================
        // PDF
        // =================================================

        if (
            extension === "pdf"
        ) {

            window.open(
                `/fichatecnica/ver/${file.id}`,
                "_blank"
            );

            return;

        }


        // =================================================
        // EXCEL
        // =================================================

        if (
            extension === "xls" ||
            extension === "xlsx" ||
            extension === "xlsm" ||
            extension === "csv"
        ) {

           window.open(
            `/fichatecnica/excel/${file.id}`,
            "_blank"
        );
                    return;

        }


        // =================================================
        // OTROS
        // =================================================

        window.open(
            `/archivos/descargar/${file.id}`,
            "_blank"
        );

    }


    // =================================================
    // ACCIONES CHONKY
    // =================================================

    function handleAction(data) {

        console.log(
            "================click================="
        );

        console.log(
            "FICHA ACCIÓN CHONKY:",
            data.id
        );


        // =================================================
        // ABRIR ARCHIVO / CARPETA
        // =================================================

        if (
            data.id ===
            ChonkyActions.OpenFiles.id
        ) {

            const selectedFile =
                data.payload?.files?.[0];


            if (!selectedFile) {

                console.log(
                    "FICHA: no hay archivo seleccionado"
                );

                return;

            }


            console.log(
                "FICHA SELECCIONADO:",
                selectedFile
            );


            // ---------------------------------------------
            // CARPETA
            // ---------------------------------------------

            if (
                selectedFile.isDir
            ) {

                openFolder(
                    selectedFile
                );

                return;

            }


            // ---------------------------------------------
            // ARCHIVO
            // ---------------------------------------------

            openFile(
                selectedFile
            );

            return;

        }


        // =================================================
        // SUBIR NIVEL
        // =================================================

        if (
            data.id ===
            ChonkyActions.OpenParentFolder.id
        ) {

            goUp();

            return;

        }

    }


    // =================================================
    // RENDER
    // =================================================

    return (

        <div
            style={{
                height: "600px",
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
                        value={busquedaGlobalf}
                        onChange={(event) =>
                            setBusquedaGlobalF(
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
                            buscandoGlobalF
                        }
                        style={{
                            height: "38px",
                            padding: "0 16px",
                            border: "none",
                            borderRadius: "4px",
                            cursor: buscandoGlobalF
                                ? "wait"
                                : "pointer",
                            background: "#007bff",
                            color: "#fff",
                            fontSize: "14px",
                        }}
                    >

                        {buscandoGlobalF
                            ? "Buscando..."
                            : "Buscar"}

                    </button>

                    {resultadosGlobalesF.length > 0 && (

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

                {busquedaGlobalf.trim() && (
                    <div
                        style={{
                            marginTop: "10px",
                        }}
                    >

                        {buscandoGlobalF && (

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

                        {!buscandoGlobalF &&
                            resultadosGlobalesF.length === 0 && (

                                <div
                                    style={{
                                        padding: "10px",
                                        color: "#666",
                                        fontSize: "14px",
                                    }}
                                >
                                    No se encontraron
                                    resultados para "
                                    {busquedaGlobalf}
                                    ".
                                </div>

                            )}

                        {!buscandoGlobalF &&
                            resultadosGlobalesF.length > 0 && (

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
                                            resultadosGlobalesF.length
                                        }
                                    </div>

                                    {resultadosGlobalesF.map(
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

            <FullFileBrowser

                files={files}

                folderChain={folderChain}

                onFileAction={handleAction}

                disableDragAndDrop={true}

            />

        </div>

    );

}