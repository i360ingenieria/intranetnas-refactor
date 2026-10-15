
import DashboardHeader from "../components/DashboardHeader";
import PopupModal from "../../../components/Popup/PopupModal";
import QuickAccess from "../components/QuickAccess";
 
export default function Dashboard() {
    return (
        <div className="container-fluid">
            <DashboardHeader />

            <QuickAccess />
            <PopupModal
                title="Comunicado institucional"
                size="large"
                showOncePerSession={false}
                storageKey="comunicado-dashboard-octubre-2026"
            >
                <div>
                    <h4>Bienvenido a la intranet</h4>

                    <p>
                         <a href="http://192.168.100.31" target="_blank" rel="noopener noreferrer">Intranet Respaldo</a>
                    </p>
                </div>
            </PopupModal>
        </div>
    );
}
