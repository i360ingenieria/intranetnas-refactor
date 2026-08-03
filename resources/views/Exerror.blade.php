private function mostrarError($titulo, $detalle)
{
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4>❌ $titulo</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <pre style="white-space: pre-wrap;">$detalle</pre>
                            </div>
                            <hr>
                            <h6>Información de depuración:</h6>
                            <ul>
                                <li>Ruta completa: <code>$detalle</code></li>
                                <li>¿Existe el archivo? <code>{" (" . (file_exists($detalle) ? "Sí" : "No") . ")"}</code></li>
                                <li>Permisos: <code>" . (file_exists($detalle) ? substr(sprintf('%o', fileperms($detalle)), -4) : "N/A") . "</code></li>
                            </ul>
                            <hr>
                            <div class="text-center">
                                <a href="javascript:history.back()" class="btn btn-primary">← Volver</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    HTML;
}