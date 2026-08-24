import { createBrowserRouter } from "react-router-dom";

import AdminLayout from "../layouts/AdminLayout";

import Dashboard from "../modules/dashboard/pages/Dashboard";
import Explorer from "../modules/explorer/pages/Explorer";
import FichaTecnica from "../modules/fichatecnica/pages/FichaTecnica";
import ExcelViewerPage from "../modules/fichatecnica/pages/ExcelViewerPage";


const router = createBrowserRouter([
    {
        path: "/",

        element: <AdminLayout />,

        children: [

            {
                index: true,
                element: <Dashboard />,
            },

            {
                path: "explorer",
                element: <Explorer />,
            },

            {
                path: "fichatecnica",
                element: <FichaTecnica />,
            },

            {
                path: "fichatecnica/excel/:id",
                element: <ExcelViewerPage />,
            },

        ],
    },
]);


export { router };