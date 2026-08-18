export default function StatCard({
    title,
    value,
    icon,
    color = "primary",
}) {
    return (
        <div className="col-lg-3 col-md-6 mb-4">

            <div className={`small-box bg-${color}`}>

                <div className="inner">

                    <h3>{value}</h3>

                    <p>{title}</p>

                </div>

                <div className="icon">
                    <i className={icon}></i>
                </div>

            </div>

        </div>
    );
}