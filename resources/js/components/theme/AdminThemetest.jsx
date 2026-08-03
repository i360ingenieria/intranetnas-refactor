import Header from "./Header";
import Sidebar from "./Sidebar";
import Footer from "./Footer";

export default function AdminTheme() {
    return (
        <div className="app-wrapper">
            <Sidebar />

            <main className="app-main">
                <Header />

                <div className="content-wrapper p-3">
                    <h1>Dashboard</h1>
                </div>

                <Footer />
            </main>
        </div>
    );
}