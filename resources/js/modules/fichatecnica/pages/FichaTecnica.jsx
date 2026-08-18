import { useEffect } from "react";

import {
    FullFileBrowser,
    ChonkyActions
} from "chonky";

import { getFichaTecnica } from "../services/fichatecnicaService";
import { toChonkyFiles } from "../adapter/fichatecnicaAdapter";
import { useFichaTecnicaStore } from "../store/fichatecnicaStore";

export default function FichaTecnica() {

    const {
        basePath,
        files,
        setFiles,
        setBasePath
    } = useFichaTecnicaStore();


    // =====================================================
    // CARGAR ARCHIVOS
    // =====================================================

    useEffect(() => {

        loadFiles();

    }, [basePath]);


    async function loadFiles() {

        try {

            const data = await getFichaTecnica(basePath);

            console.log("FICHA API:", data);

            const chonkyFiles = toChonkyFiles(data);

            console.log("FICHA CHONKY:", chonkyFiles);

            setFiles(chonkyFiles);

        } catch (error) {

            console.error(
                "ERROR FICHA TECNICA:",
                error
            );

        }

    }


    // =====================================================
    // ACCIONES CHONKY
    // =====================================================

    function handleAction(data) {

        console.log(
            "FICHA ACCION:",
            data.id
        );


        // =================================================
        // VOLVER
        // =================================================

        if (
            data.id === ChonkyActions.GoBack.id
        ) {

            goBack();

            return;
        }


        // =================================================
        // SUBIR NIVEL
        // =================================================

        if (
            data.id === ChonkyActions.GoUp.id
        ) {

            goUp();

            return;
        }


        // =================================================
        // ABRIR ARCHIVO / CARPETA
        // =================================================

        if (
            data.id !== ChonkyActions.OpenFiles.id
        ) {

            return;
        }


        const file =
            data.payload?.files?.[0];


        if (!file) {

            return;

        }


        console.log(
            "FICHA ABIERTO:",
            file
        );


        // =================================================
        // CARPETA
        // =================================================

        if (file.isDir) {

            const ruta =
                file.extraData?.ruta;

            if (!ruta) {

                console.error(
                    "La carpeta no tiene ruta:",
                    file
                );

                return;
            }


            console.log(
                "ENTRANDO A:",
                ruta
            );


            setBasePath(ruta);

            return;
        }


        // =================================================
        // ARCHIVO
        // =================================================

        const ruta =
            file.extraData?.ruta;

        const extension =
            (
                file.extraData?.extension ||
                file.name.split(".").pop() ||
                ""
            ).toLowerCase();


        console.log(
            "ARCHIVO:",
            file.name
        );

        console.log(
            "ID:",
            file.id
        );

        console.log(
            "RUTA:",
            ruta
        );

        console.log(
            "EXTENSION:",
            extension
        );


        // =================================================
        // PDF
        // =================================================

        if (extension === "pdf") {

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
            extension === "xlsx" ||
            extension === "xls"
        ) {

            if (!ruta) {

                console.error(
                    "No existe ruta para Excel"
                );

                return;
            }


            window.open(
                `/excel/ver/${encodeURIComponent(ruta)}`,
                "_blank"
            );

            return;
        }


        // =================================================
        // OTROS ARCHIVOS
        // =================================================

        console.log(
            "Tipo de archivo no soportado:",
            extension
        );

    }


    // =====================================================
    // VOLVER AL PADRE
    // =====================================================

    function goBack() {

        const root =
            "/mnt/nas_pcmercadeo/";


        if (
            !basePath ||
            basePath === root ||
            basePath === "/mnt/nas_pcmercadeo"
        ) {

            console.log(
                "Ya estamos en la raíz"
            );

            return;
        }


        const limpio =
            basePath.replace(/\/$/, "");


        const posicion =
            limpio.lastIndexOf("/");


        if (posicion <= 0) {

            setBasePath(root);

            return;
        }


        const padre =
            limpio.substring(
                0,
                posicion
            );


        console.log(
            "VOLVIENDO A:",
            padre
        );


        setBasePath(
            padre || root
        );

    }


    // =====================================================
    // SUBIR NIVEL
    // =====================================================

    function goUp() {

        goBack();

    }


    // =====================================================
    // RENDER
    // =====================================================

    return (

        <div
            style={{
                height: "600px",
                width: "100%"
            }}
        >

            <FullFileBrowser

                files={files}

                onFileAction={handleAction}

            />

        </div>

    );

}