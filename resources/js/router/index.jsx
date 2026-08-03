import { BrowserRouter, Routes, Route } from "react-router-dom";

import Dashboard from "../pages/Dashboard";
import AdminLayout from "../layouts/AdminLayout";

export default function Router() {
    return (
        <BrowserRouter basename="/react">
            <Routes>
                <Route
                    path="/"
                    element={
                        <AdminLayout>
                            <Dashboard />
                        </AdminLayout>
                    }
                />
            </Routes>
        </BrowserRouter>
    );
}