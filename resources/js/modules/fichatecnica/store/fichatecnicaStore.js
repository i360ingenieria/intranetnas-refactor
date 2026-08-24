import { create } from "zustand";


const ROOT_PATH =
    "/mnt/nas_pcmercadeo/";


export const useFichaTecnicaStore = create(
    (set) => ({

        basePath: ROOT_PATH,

        files: [],


        setBasePath: (basePath) =>
            set({
                basePath:
                    basePath || ROOT_PATH,
            }),


        setFiles: (files) =>
            set({
                files:
                    Array.isArray(files)
                        ? files
                        : [],
            }),

    })
);