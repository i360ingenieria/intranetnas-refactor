
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-title', 'Dashboard')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection

@section('css')
<style>
    .users-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding: 0;
        margin: 0 0 20px 0;
        list-style: none;
        justify-content: center;
    }

    .users-list>li {
        background: #fff;
        border-radius: 8px;
        padding: 15px 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 280px;
        box-sizing: border-box;
    }

    .users-list>li img {
        width: 140px;
        height: 120px;
        object-fit: contain;
        margin-bottom: 15px;
        display: block;
    }

    .image-title {
        display: block;
        font-weight: bold;
        font-size: 15px;
        color: #333;
        margin-bottom: 5px;
        line-height: 1.3;
        width: 100%;
        word-wrap: break-word;
    }

    .image-subtitle {
        display: block;
        font-size: 12px;
        color: #666;
        line-height: 1.4;
        width: 100%;
        word-wrap: break-word;
    }

    /* Mejora visual para las tarjetas */
    .info-box {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .brand-link {
        display: block;
        width: 100%;
        height: 100%;
        text-decoration: none;
    }

    .brand-link img {
        object-fit: contain;
        max-height: 80px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .users-list {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            justify-content: center;
        }

        .users-list>li {
            height: 260px;
            padding: 10px;
        }

        .users-list>li img {
            width: 120px;
            height: 100px;
        }
    }
</style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Plataformas Internas</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <!-- Fila 1 -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <a href="http://190.85.192.138:22224/signin" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/lapub.png') }}" width="100%" height="100%" alt="LabSense">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <a href="http://192.168.100.141/Panacea/LogOnForm.aspx?s=1" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/panaceafac.png') }}" width="100%" height="100%" alt="Panacea FAC">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <a href="http://192.168.100.42/Panacea/LogOnForm.aspx?s=1" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/odonto.png') }}" width="100%" height="100%" alt="Panacea Odonto">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <a href="http://192.168.100.17/Panacea/LogOnForm.aspx?s=1" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/palterno.png') }}" width="100%" height="100%" alt="Panacea Alterno">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2 -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger">
                                    <a href="http://192.168.100.9/Panacea/LogOnForm.aspx?s=1" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/panacea.png') }}" width="100%" height="100%" alt="Panacea">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-5 col-10">
                                <div class="info-box bg-info">
                                    <a href="https://mipres.sispro.gov.co/MIPRESNOPBS/Login.aspx?ReturnUrl=fMIPRESNOPBSfLogoff.aspx" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/mpress.png') }}" width="100%" height="100%" alt="MIPRES">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <a href="http://192.168.104.215/portal/Login.aspx?ReturnUrl=%2fportal%2f" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/imo.png') }}" width="100%" height="100%" alt="IMO">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <a href="http://192.168.104.225:5300/ords/optimo/r/optimo/login" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/visor.png') }}" width="100%" height="100%" alt="Visor">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 3 -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger">
                                    <a href="http://190.85.192.138:801/MyLogin.aspx" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/enter.png') }}" width="100%" height="100%" alt="Enterprise">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info">
                                    <a href="http://192.168.100.240:801/MyLogin.aspx" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/enterp.png') }}" width="100%" height="100%" alt="Enterprise">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success">
                                    <a href="http://192.168.100.105:5300/apex/f?p=140:101" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/hstm.png') }}" width="100%" height="100%" alt="HSTM">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <a href="https://hospitalsantamonica.qpasa.com.co/#/login" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/qpsa.png') }}" width="100%" height="100%" alt="QPSA">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                        </div>
                             <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger">
                                    <a href="http://192.168.100.108:5300/apex/f?p=106:LOGIN_DESKTOP::::::" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/hclinica.png') }}" width="100%" height="100%" alt="SASA">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                        <!-- Fila 4 -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <a href="https://dnahsm.netlify.app" class="brand-link" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('dist/img/desnutricion.png') }}" width="100%" height="100%" alt="Desnutrición">
                                    </a>
                                    <div class="info-box-content"></div>
                                </div>
                            </div>
                        
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="javascript:void(0)" class="uppercase">Ver todas las plataformas</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Popup de Bienvenida -->
 <div class="modal fade" id="popupInicio" tabindex="-1" role="dialog" aria-labelledby="popupInicioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg" style="min-height:50vh; border-radius:15px; overflow:hidden;">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="popupInicioLabel">
                    📢 Aviso Importante    
                </h4>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center p-4">
                <div>
                    <!-- SECCIÓN DE TARJETAS CHULAS -->
                    <div class="row my-4 text-left">
                        <!-- Tarjeta Roja (Crítico / Alerta) -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #dc3545; background-color: #fdf2f2; border-radius: 8px;">
                                <div class="card-body p-3">
                                    <h5 class="card-title text-danger font-weight-bold mb-2">
                                        🚨 Alerta  
                                    </h5>
                                    <p class="card-text text-dark small" style="line-height: 1.4;">
                                        <p class="mb-4 text-muted" style="font-size:17px;">
                                          Directorio actualizado 
                                        </p>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta Amarilla (Advertencia / Pendiente) -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #ffc107; background-color: #fffdf2; border-radius: 8px;">
                                <div class="card-body p-3">
                                    <h5 class="card-title text-warning font-weight-bold mb-2" style="color: #856404 !important;">
                                        ⚠️ Recordatorio
                                    </h5>
                                    <p class="card-text text-dark small" style="line-height: 1.4;">
                                       <h2>Importante</h2> hacer limpieza de  cache del navegador para que se actualicen los cambios en la intranet, cualquier duda o sugerencia no dudes en comunicarte con el equipo de informática.
                                    </p>
                                      <img src="{{ asset('dist/img/limpiar.png') }}" width="100%" height="100%" alt="Desnutrición">
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta Verde (Éxito / Informativo / Bueno) -->
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm" style="border-left: 5px solid #28a745; background-color: #f2fdf4; border-radius: 8px;">
                                <div class="card-body p-3">
                                    <h5 class="card-title text-success font-weight-bold mb-2">
                                        ✅ Buenas Noticias
                                    </h5>
                                    <p class="card-text text-dark small" style="line-height: 1.4;">
                                       cambio que se veran en la nueva web del hospital <a href="https://new-web.hospitalsantamonica.gov.co/" target="_blank">la proxima cara de la web hospitalaria</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- FIN DE SECCIÓN DE TARJETAS -->
                      
                    
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-lg px-5" data-dismiss="modal" style="border-radius: 8px;">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    $('#popupInicio').modal('show');
});
</script>
@endpush