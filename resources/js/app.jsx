import "./assets/css/adminlte.css";
import "./assets/css/index.css";
import { setChonkyDefaults } from "chonky";
import { ChonkyIconFA } from "chonky-icon-fontawesome";

setChonkyDefaults({
    iconComponent: ChonkyIconFA
});
import React from "react";
import ReactDOM from "react-dom/client";
import { RouterProvider } from "react-router-dom";
import { router } from "./router";

ReactDOM.createRoot(document.getElementById("app")).render(
    <RouterProvider router={router} />
);