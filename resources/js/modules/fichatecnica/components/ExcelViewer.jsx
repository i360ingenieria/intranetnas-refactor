import { useEffect, useMemo, useState } from "react";

import excelService from "../services/excelService";

export default function ExcelViewer({ fileId, onClose }) {
    const [workbook, setWorkbook] = useState(null);
    const [currentSheet, setCurrentSheet] = useState(0);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // =====================================================
    // CARGAR EXCEL
    // =====================================================

    useEffect(() => {
        let cancelled = false;

        async function loadExcel() {
            try {
                setLoading(true);
                setError(null);

                const data =
                    await excelService.getWorkbook(fileId);

                if (cancelled) {
                    return;
                }

                setWorkbook(data);
                setCurrentSheet(0);
                setSearch("");
            } catch (err) {
                if (cancelled) {
                    return;
                }

                console.error(
                    "ERROR CARGANDO EXCEL:",
                    err
                );

                setError(
                    err?.message ||
                        "No fue posible cargar el archivo Excel."
                );
            } finally {
                if (!cancelled) {
                    setLoading(false);
                }
            }
        }

        if (!fileId) {
            setError(
                "No se proporcionó el ID del archivo."
            );
            setLoading(false);
            return;
        }

        loadExcel();

        return () => {
            cancelled = true;
        };
    }, [fileId]);


     const descargarArchivo = () => {
        if (!fileId) {
            console.error("No existe fileId para descargar");
            return;
        }

        window.open(`/fichatecnica/descargar/${fileId}`, "_blank");
    };
    // =====================================================
    // HOJA ACTUAL
    // =====================================================

    const sheet =
        workbook?.sheets?.[currentSheet] || null;

    const headers =
        sheet?.headers || [];

    const rows =
        sheet?.rows || [];
 
    // =====================================================
    // FILTRAR
    // =====================================================

    const filteredRows = useMemo(() => {
        const text = search.trim().toLowerCase();

        if (!text) {
            return rows;
        }

        return rows.filter((row) =>
            row.some((cell) =>
                String(cell ?? "")
                    .toLowerCase()
                    .includes(text)
            )
        );
    }, [rows, search]);

    // =====================================================
    // LOADING
    // =====================================================

    if (loading) {
        return (
            <div className="card">
                <div
                    className="card-body text-center"
                    style={{
                        padding: "80px",
                    }}
                >
                    <i
                        className="fas fa-spinner fa-spin"
                        style={{
                            fontSize: "48px",
                        }}
                    />

                    <p className="mt-3 mb-0">
                        Cargando archivo Excel...
                    </p>
                </div>
            </div>
        );
    }

    // =====================================================
    // ERROR
    // =====================================================

    if (error) {
        return (
            <div className="card">
                <div className="card-header">
                    <h3 className="card-title">
                        <i className="fas fa-file-excel mr-2" />
                        Visor Excel
                    </h3>
                </div>

                <div className="card-body">
                    <div className="alert alert-danger">
                        <i className="fas fa-exclamation-triangle mr-2" />

                        {error}
                    </div>
                  
                    <button
                        type="button"
                        className="btn btn-secondary"
                        onClick={onClose}
                    >
                        <i className="fas fa-arrow-left mr-2" />
                        Volver
                    </button>
                </div>
            </div>
        );
    }

    // =====================================================
    // SIN DATOS
    // =====================================================

    if (
        !workbook ||
        !Array.isArray(workbook.sheets) ||
        workbook.sheets.length === 0
    ) {
        return (
            <div className="card">
                <div className="card-body">
                    <div className="alert alert-warning">
                        El archivo no contiene hojas
                        para mostrar.
                    </div>

                    <button
                        type="button"
                        className="btn btn-secondary"
                        onClick={onClose}
                    >
                        <i className="fas fa-arrow-left mr-2" />
                        Volver****
                    </button>
                </div>
            </div>
        );
    }

    // =====================================================
    // RENDER
    // =====================================================

    return (
        <div className="excel-viewer">

            {/* =================================================
                CABECERA
            ================================================= */}

            <div className="card">

                <div className="card-header">

                    <div className="d-flex justify-content-between align-items-center">

                        <h3 className="card-title mb-0">

                            <i className="fas fa-file-excel mr-2" />

                            {workbook.filename}

                        </h3>
                        <button
                            type="button"
                            className="btn btn-secondary btn-sm"
                            onClick={descargarArchivo}
                        >
                            <i className="fas fa-solid fa-download mr-1" />

                            descargar 
                        </button>
                        <button
                            type="button"
                            className="btn btn-secondary btn-sm"
                            onClick={onClose}
                        >
                            <i className="fas fa-arrow-left mr-1" />

                            Volver----
                        </button>

                    </div>

                </div>

                {/* =================================================
                    HOJAS
                ================================================= */}

                <div
                    className="card-body p-0"
                    style={{
                        borderBottom:
                            "1px solid #dee2e6",
                    }}
                >

                    <div
                        className="d-flex flex-wrap"
                        style={{
                            padding:
                                "10px 15px 10px",
                            gap: "5px",
                        }}
                    >

                        {workbook.sheets.map(
                            (item, index) => (
                                <button
                                    key={`${item.name}-${index}`}
                                    type="button"
                                    className={
                                        `btn btn-sm ${
                                            currentSheet === index
                                                ? "btn-success"
                                                : "btn-light"
                                        }`
                                    }
                                    onClick={() => {
                                        setCurrentSheet(
                                            index
                                        );

                                        setSearch("");
                                    }}
                                >

                                    <i className="fas fa-table mr-1" />

                                    {item.name}

                                    {" "}

                                    <span>
                                        (
                                        {Number(
                                            item.totalRows || 0
                                        ).toLocaleString()}
                                        )
                                    </span>

                                </button>
                            )
                        )}

                    </div>

                </div>

                {/* =================================================
                    TOOLBAR
                ================================================= */}

                <div className="card-body">

                    <div className="row align-items-center">

                        <div className="col-md-6 mb-2 mb-md-0">

                            <div className="input-group">

                                <div className="input-group-prepend">

                                    <span className="input-group-text">

                                        <i className="fas fa-search" />

                                    </span>

                                </div>

                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Buscar en esta hoja..."
                                    value={search}
                                    onChange={(event) =>
                                        setSearch(
                                            event.target.value
                                        )
                                    }
                                />

                            </div>

                        </div>

                        <div className="col-md-6">

                            <div className="d-flex justify-content-md-end">

                                <span className="badge badge-info mr-2 p-2">

                                    <i className="fas fa-table mr-1" />

                                    {Number(
                                        sheet?.totalRows || 0
                                    ).toLocaleString()}{" "}
                                    filas

                                </span>

                                <span className="badge badge-secondary p-2">

                                    <i className="fas fa-columns mr-1" />

                                    {headers.length}{" "}
                                    columnas

                                </span>

                            </div>

                        </div>

                    </div>

                    {/* =================================================
                        WARNING
                    ================================================= */}

                    {sheet?.warning && (
                        <div className="alert alert-warning mt-3 mb-0">

                            <i className="fas fa-exclamation-triangle mr-2" />

                            Esta hoja contiene{" "}

                            <strong>
                                {Number(
                                    sheet.totalRows || 0
                                ).toLocaleString()}
                            </strong>{" "}

                            filas.

                            Solo se muestran las primeras{" "}

                            <strong>
                                5.000
                            </strong>{" "}

                            filas.

                        </div>
                    )}

                </div>

                {/* =================================================
                    TABLA
                ================================================= */}

                <div
                    className="table-responsive"
                    style={{
                        maxHeight: "65vh",
                        overflow: "auto",
                    }}
                >

                    <table
                        className="table table-bordered table-hover table-sm mb-0"
                    >

                        <thead
                            style={{
                                position: "sticky",
                                top: 0,
                                zIndex: 10,
                            }}
                        >

                            <tr>

                                <th
                                    style={{
                                        backgroundColor:
                                            "#2c3e50",
                                        color: "#fff",
                                        minWidth: "60px",
                                    }}
                                >
                                    #
                                </th>

                                {headers.map(
                                    (header, index) => (
                                        <th
                                            key={index}
                                            style={{
                                                backgroundColor:
                                                    [
                                                        "#2c3e50",
                                                        "#16a085",
                                                        "#e67e22",
                                                        "#9b59b6",
                                                        "#3498db",
                                                        "#e74c3c",
                                                        "#1abc9c",
                                                        "#f39c12",
                                                    ][
                                                        index % 8
                                                    ],
                                                color:
                                                    "#fff",
                                                whiteSpace:
                                                    "nowrap",
                                            }}
                                        >
                                            {header ||
                                                `Columna ${
                                                    index + 1
                                                }`}
                                        </th>
                                    )
                                )}

                            </tr>

                        </thead>

                        <tbody>

                            {filteredRows.map(
                                (row, rowIndex) => (
                                    <tr
                                        key={rowIndex}
                                    >

                                        <td
                                            style={{
                                                fontWeight:
                                                    600,
                                                backgroundColor:
                                                    "#f8f9fa",
                                            }}
                                        >
                                            {rowIndex + 1}
                                        </td>

                                        {headers.map(
                                            (_, columnIndex) => (
                                                <td
                                                    key={
                                                        columnIndex
                                                    }
                                                >
                                                    {
                                                        row[
                                                            columnIndex
                                                        ]
                                                    }
                                                </td>
                                            )
                                        )}

                                    </tr>
                                )
                            )}

                            {filteredRows.length === 0 && (
                                <tr>

                                    <td
                                        colSpan={
                                            headers.length + 1
                                        }
                                        className="text-center text-muted"
                                        style={{
                                            padding: "40px",
                                        }}
                                    >

                                        <i className="fas fa-search mb-2" />

                                        <br />

                                        No se encontraron
                                        registros.

                                    </td>

                                </tr>
                            )}

                        </tbody>

                    </table>

                </div>

                {/* =================================================
                    FOOTER
                ================================================= */}

                <div className="card-footer">

                    <div className="d-flex justify-content-between">

                        <span className="text-muted">

                            <i className="fas fa-info-circle mr-1" />

                            {search
                                ? `Mostrando ${filteredRows.length.toLocaleString()} de ${rows.length.toLocaleString()} filas`
                                : `Mostrando ${rows.length.toLocaleString()} filas`
                            }

                        </span>

                        <span className="text-muted">

                            Hoja{" "}
                            {currentSheet + 1}{" "}
                            de{" "}
                            {workbook.totalSheets}

                        </span>

                    </div>

                </div>

            </div>

        </div>
    );
}