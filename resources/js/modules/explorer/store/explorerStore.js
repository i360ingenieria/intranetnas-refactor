import { create } from "zustand";


const ROOT = "/mnt/intranet/sistema gestion de calidad";

export const useExplorerStore = create((set) => ({
    basePath: ROOT,
    search: "",
    files: [],
    loading: false,

    setFiles: (files) => set({ files }),

    setBasePath: (basePath) => set({ basePath }),

    setSearch: (search) => set({ search }),

    setLoading: (loading) => set({ loading }),
}));