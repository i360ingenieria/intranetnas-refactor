// 1. Importaciones de librerías principales (React, React Router, Chonky)
import React from "react";
import ReactDOM from "react-dom/client";
import { RouterProvider } from "react-router-dom";
import { setChonkyDefaults } from "chonky";
import { ChonkyIconFA } from "chonky-icon-fontawesome";

// 2. Importación de rutas
import { router } from "./router";

// 3. Importaciones de estilos (CSS)
import "./assets/css/adminlte.css";
import "./assets/css/index.css";
import "./assets/css/chonkySidebar.css";

// 4. Configuración inicial
setChonkyDefaults({
    iconComponent: ChonkyIconFA
});

// 5. Renderizado de la aplicación
ReactDOM.createRoot(document.getElementById("app")).render(
    <RouterProvider router={router} />
);