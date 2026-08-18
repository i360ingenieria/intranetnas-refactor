import { useEffect, useState } from "react";
import { getDashboard } from "../services/dashboardService";
import StatCard from "./StatCard";

export default function DashboardStats() {
    const [stats, setStats] = useState(null);

    useEffect(() => {
        loadStats();
    }, []);

    async function loadStats() {
        try {
            const data = await getDashboard();
            setStats(data);
        } catch (error) {
            console.error("Error cargando dashboard:", error);
        }
    }

    if (!stats) {
        return <p>Cargando indicadores...</p>;
    }

    return (
        <div className="row">
            <StatCard
                title="Archivos"
                value={stats.archivos.toLocaleString()}
                icon="fas fa-file"
                color="primary"
            />

            <StatCard
                title="Carpetas"
                value={stats.carpetas.toLocaleString()}
                icon="fas fa-folder"
                color="success"
            />

            <StatCard
                title="Fichas"
                value={stats.fichas.toLocaleString()}
                icon="fas fa-file-pdf"
                color="warning"
            />

            <StatCard
                title="Última indexación"
                value={stats.ultima_actualizacion}
                icon="fas fa-clock"
                color="danger"
            />
        </div>
    );
}