import DashboardHeader from "../components/DashboardHeader";
import DashboardStats from "../components/DashboardStats";
import QuickAccess from "../components/QuickAccess";
import { getDashboard } from "../services/dashboardService";
export default function Dashboard() {
    return (
        <div className="container-fluid">
            <DashboardHeader />
            <DashboardStats />
            <QuickAccess />
        </div>
    );
}