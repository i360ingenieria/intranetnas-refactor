
import { useEffect, useState } from "react";
import "./PopupModal.css";

export default function PopupModal({
    title = "Información",
    children,
    onClose,
    showOncePerSession = false,
    storageKey = "intranet-popup",
    closeOnBackdrop = true,
    closeOnEscape = true,
    footer = null,
    size = "medium",
}) {
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        if (showOncePerSession) {
            try {
                if (sessionStorage.getItem(storageKey) === "shown") {
                    return;
                }

                sessionStorage.setItem(storageKey, "shown");
            } catch (error) {
                console.warn(
                    "No se pudo consultar sessionStorage:",
                    error
                );
            }
        }

        setVisible(true);
    }, [showOncePerSession, storageKey]);

    useEffect(() => {
        if (!visible || !closeOnEscape) {
            return;
        }

        const handleKeyDown = (event) => {
            if (event.key === "Escape") {
                handleClose();
            }
        };

        document.addEventListener("keydown", handleKeyDown);

        return () => {
            document.removeEventListener("keydown", handleKeyDown);
        };
    }, [visible, closeOnEscape]);

    const handleClose = () => {
        setVisible(false);

        if (typeof onClose === "function") {
            onClose();
        }
    };

    if (!visible) {
        return null;
    }

    return (
        <div
            className="intranet-popup-overlay"
            onMouseDown={(event) => {
                if (
                    closeOnBackdrop &&
                    event.target === event.currentTarget
                ) {
                    handleClose();
                }
            }}
        >
            <section
                className={`intranet-popup intranet-popup--${size}`}
                role="dialog"
                aria-modal="true"
                aria-labelledby="intranet-popup-title"
            >
                <header className="intranet-popup__header">
                    <h2
                        id="intranet-popup-title"
                        className="intranet-popup__title"
                    >
                        {title}
                    </h2>

                    <button
                        type="button"
                        className="intranet-popup__close"
                        onClick={handleClose}
                        aria-label="Cerrar ventana"
                        title="Cerrar"
                    >
                        ×
                    </button>
                </header>

                <div className="intranet-popup__body">
                    {children}
                </div>

                {footer && (
                    <footer className="intranet-popup__footer">
                        {footer}
                    </footer>
                )}
            </section>
        </div>
    );
}
