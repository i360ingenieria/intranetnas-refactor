import { memo, useEffect, useRef, useState } from "react";
import { NavLink } from "react-router-dom";

const SidebarNavList = ({ data, submenu }) => {
    const [isMenuExtended, setIsMenuExtended] = useState(false);

    const menuRef = useRef(null);

    const hasChildren =
        Array.isArray(data?.children) && data.children.length > 0;

    const isSubmenu = submenu === "active";

    const icon = data?.icon ? (
        <i className={data.icon}></i>
    ) : null;

    const handleToggle = (event) => {
        event.preventDefault();

        setIsMenuExtended((prev) => !prev);
    };

    useEffect(() => {
        const menuElement = menuRef.current;

        if (!menuElement) {
            return;
        }

        if (isMenuExtended) {
            menuElement.classList.add("show");

            menuElement.style.display = "block";
            menuElement.style.maxHeight =
                menuElement.scrollHeight + "px";
            menuElement.style.opacity = "1";
            menuElement.style.overflow = "hidden";

            const timer = setTimeout(() => {
                menuElement.style.maxHeight = "none";
            }, 300);

            return () => clearTimeout(timer);
        }

        menuElement.style.maxHeight =
            menuElement.scrollHeight + "px";

        menuElement.style.opacity = "1";
        menuElement.style.overflow = "hidden";

        requestAnimationFrame(() => {
            menuElement.style.maxHeight = "0px";
            menuElement.style.opacity = "0";
        });

        const timer = setTimeout(() => {
            menuElement.classList.remove("show");
            menuElement.style.display = "none";
        }, 300);

        return () => clearTimeout(timer);
    }, [isMenuExtended]);

    // =====================================================
    // NAV HEADER
    // =====================================================

    if (data?.navheader) {
        return (
            <li className="nav-header">
                {data.title}
            </li>
        );
    }

    // =====================================================
    // ITEM CON HIJOS
    // =====================================================

    if (hasChildren) {
        return (
            <li
                className={`nav-item ${
                    isMenuExtended ? "menu-open" : ""
                }`}
            >
                <a
                    href="#"
                    className="nav-link"
                    onClick={handleToggle}
                    style={{ cursor: "pointer" }}
                >
                    {icon}

                    <p>
                        {data.title}

                        <i
                            className={`nav-arrow fas ${
                                isMenuExtended
                                    ? "fa-angle-down"
                                    : "fa-angle-right"
                            }`}
                        ></i>
                    </p>
                </a>

                <ul
                    ref={menuRef}
                    className="nav nav-treeview"
                    style={{
                        display: "none",
                        maxHeight: "0px",
                        opacity: 0,
                        overflow: "hidden",
                        transition:
                            "max-height 0.3s ease, opacity 0.3s ease",
                    }}
                >
                    {data.children.map((child, index) => (
                        <SidebarNavList
                            data={child}
                            key={`${child.title}-${index}`}
                            submenu="active"
                        />
                    ))}
                </ul>
            </li>
        );
    }

    // =====================================================
    // ITEM NORMAL / HOJA
    // =====================================================

    return (
        <li className="nav-item">
            <NavLink
                to={data?.path || "/"}
                className={({ isActive }) =>
                    `nav-link ${isActive ? "active" : ""}`
                }
            >
                {isSubmenu && (
                    <i className="far fa-circle nav-icon"></i>
                )}

                {icon}

                <p>{data?.title}</p>
            </NavLink>
        </li>
    );
};

export default memo(SidebarNavList);