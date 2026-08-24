import { useEffect, useMemo } from "react";

import {
    FullFileBrowser,
    ChonkyActions,
} from "chonky";

import { getFichaTecnica } from "../services/fichatecnicaService";
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

    // Quitar slash final para trabajar
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
                    "================================="
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

        console.log(
            "FICHA: archivo no compatible con visor:",
            extension
        );

    }


    // =================================================
    // ACCIONES CHONKY
    // =================================================

    function handleAction(data) {

        console.log(
            "================================="
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

            <FullFileBrowser

                files={files}

                folderChain={folderChain}

                onFileAction={handleAction}

                disableDragAndDrop={true}

            />

        </div>

    );

}