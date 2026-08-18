export default function DashboardHeader() {

    const hour = new Date().getHours();

    let saludo = "Buenas noches";

    if (hour < 12) {
        saludo = "Buenos días";
    } else if (hour < 18) {
        saludo = "Buenas tardes";
    }

    const fecha = new Date().toLocaleDateString("es-CO", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    });

    return (
        <div className="mb-4">

            <h2>{saludo}</h2>

            <h5 className="text-muted">
                Hospital Santa Mónica
            </h5>

            <small className="text-secondary">
                {fecha}
            </small>

        </div>
    );
}