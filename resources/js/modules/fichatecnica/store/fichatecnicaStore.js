import { create } from "zustand";

export const useFichaTecnicaStore = create((set) => ({
    basePath: "/mnt/nas_pcmercadeo/",
    files: [],

    setFiles: (files) =>
        set({
            files,
        }),

    setBasePath: (basePath) =>
        set({
            basePath,
        }),
}));