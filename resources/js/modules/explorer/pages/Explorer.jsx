import { useEffect, useMemo } from "react";

import {
    FullFileBrowser,
    ChonkyActions,
} from "chonky";

import { getExplorer } from "../services/explorerService";
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


    // --------------------------------------------------
    // Cargar archivos cuando cambia la carpeta
    // --------------------------------------------------

    useEffect(() => {

        loadFiles();

    }, [basePath]);


    // --------------------------------------------------
    // Cargar archivos
    // --------------------------------------------------

    async function loadFiles() {

        try {

            const data =
                await getExplorer(
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


    // --------------------------------------------------
    // Construir navegación de carpetas
    // --------------------------------------------------

    const folderChain = useMemo(() => {

        return buildFolderChain(
            basePath || ROOT_PATH
        );

    }, [basePath]);


    // --------------------------------------------------
    // ACCIONES CHONKY
    // --------------------------------------------------

   function handleAction(data) {

    console.log("===== ACCIÓN CHONKY =====");
    console.log("ID:", data.id);
    console.log("PAYLOAD:", data.payload);

    // =================================================
    // ABRIR ARCHIVO / CARPETA
    // =================================================

    if (data.id === ChonkyActions.OpenFiles.id) {

        const file = data.payload?.files?.[0];

        if (!file) {
            console.log("No hay archivo seleccionado");
            return;
        }

        console.log("SELECCIONADO:", file);

        // ---------------------------------------------
        // CARPETA
        // ---------------------------------------------

        if (file.isDir) {

            const ruta = file.extraData?.ruta;

            console.log("CARPETA:", file.name);
            console.log("RUTA:", ruta);

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

        // ---------------------------------------------
        // ARCHIVO
        // ---------------------------------------------

        abrirArchivo(file);

        return;
    }


    function abrirArchivo(file) {

    const extension =
        file.name
            ?.split(".")
            .pop()
            ?.toLowerCase();

    console.log("ABRIENDO ARCHIVO:", file.name);
    console.log("ID:", file.id);
    console.log("EXTENSION:", extension);

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
        extension === "xlsm"
    ) {

        window.open(
            `/excel/ver?id=${file.id}`,
            "_blank"
        );

        return;
    }

    // Otros
    window.open(
        `/archivos/descargar/${file.id}`,
        "_blank"
    );
}
    // =================================================
    // SUBIR UN NIVEL
    // =================================================

    if (
        data.id === ChonkyActions.OpenParentFolder.id
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
            limpio.substring(0, posicion);

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
                 *
                 */
                disableDragAndDrop={true}

            />

        </div>

    );

}