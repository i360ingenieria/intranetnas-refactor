export default function AdminLayout({ children }) {
    return (
        <div style={{ minHeight: "100vh" }}>
            <header
                style={{
                    background: "#0d6efd",
                    color: "#fff",
                    padding: "15px"
                }}
            >
                Hospital Santa Mónica
            </header>

            <main style={{ padding: "20px" }}>
                {children}
            </main>

            <footer
                style={{
                    background: "#f4f4f4",
                    padding: "10px",
                    textAlign: "center"
                }}
            >
                Intranet React
            </footer>
        </div>
    );
}