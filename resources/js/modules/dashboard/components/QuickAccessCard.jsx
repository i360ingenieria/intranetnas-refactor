export default function QuickAccessCard({ platform }) {
    return (
        <div className="col-lg-3 col-md-4 col-sm-6 mb-4">

          <div className="card shadow-sm h-100">
                <div className="card-img-container">
                    <img
                        src={`${platform.url}/${platform.logo}`}
                        alt={platform.nombre}
                        className="card-img-top"
                    />
                </div>

                <div className="card-body text-center">
                    <h5>{platform.nombre}</h5>

                    <span className={`badge bg-${platform.color}`}>
                        {platform.categoria}
                    </span>

                    <div className="mt-3">
                        <a
                            href={platform.urlaccesp}
                            target="_blank"
                            rel="noreferrer"
                            className="btn btn-primary w-100"
                        >
                            Abrir
                        </a>
                    </div>
                </div>
            </div>

        </div>
    );
}