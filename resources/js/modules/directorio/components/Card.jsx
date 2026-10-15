
import "./Card.css";

function Card({ excelddata }) {
    return (
        <article className="card">
            <img
                className="card__image"
                src={image}
                alt={title}
            />

            <div className="card__body">
                <h3 className="card__title">
                    {title}
                </h3>

                <p className="card__description">
                    {description}
                </p>

                <button
                    className="card__button"
                    onClick={onAction}
                    type="button"
                >
                    Ver perfil
                </button>
            </div>
        </article>
    );
}

export default Card;