<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR_C0NF1M4N14');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
?>
<!DOCTYPE html>
<html>
<!-- head -->
<?php require_once("../header.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed">
	<?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- BOX DE LA MIGA DE PAN -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>Catalogo de Productos</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Catalogo de Productos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- ======= Services Section ======= -->
            <div class="container" data-aos="fade-up">

                <div class="tab-content">

                    <div class="tab-pane active show" id="userlog">
                        <section class="section-bg">                      

                            <div class="container" data-aos="fade-up">
                
                                <div class="table-responsive col-xl">
                                    <!--begin::Table Widget 6-->
                                    <div class="card text-bg-dark mb-3">
                                        <!--begin::Header-->
                                        <div class="card-header border-0 pt-5">
                                            <form id="searchform" class="form-horizontal" method="POST">
                                                <div class="form-group row">
                                                    <div class="col-sm-7">                                                        
                                                        <div class="form-check form-check-inline">
                                                            <select class="custom-select" name="marca" id="substamp" style="width: 100%;" multiple required>
                                                                <!-- la lista de marcas se carga por ajax -->
                                                            </select>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <select class="custom-select" name="orden" id="price" style="width: 100%;" required>
                                                                <option selected value="1">Precio 1</option>
                                                                <option value="2">Precio 2</option>
                                                                <option value="3">Precio 3</option>	
                                                            </select>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <div class="form-check">
                                                                <input id="existe" class="form-check-input" type="checkbox">
                                                                <label class="form-check-label" for="flexCheckDefault">Solo Con Existencia</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="d-grid gap-3 d-md-flex justify-content-md-end">                                        
                                                            <button id="btnsearch" type="submit"  class="btn btn-outline-primary btn-light"><i class="bi bi-search"></i> <span>Buscar</span></button>    
                                                        </div>
                                                        <div id="optionbody" class="card-body">
                                                            <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <a id="btncatalogue" class="btn btn-outline-info text-center" role="button" aria-pressed="true" Target="_blank">
                                                                                    <img src="../../public/build/images/catalogue.png" class="card-img-top" alt="...">
                                                                                    <label for=""><strong>Catalogo</strong></label>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <a id="btnlist" class="btn btn-outline-info text-center" role="button" aria-pressed="true" Target="_blank">
                                                                                    <img src="../../public/build/images/list.png" class="card-img-top" alt="...">
                                                                                    <label for=""><strong>lista</strong></label>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>  
                                                            </div>  
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                    
                                                    
                                                    
                                                    
                                            </form>
                                            
                                        </div>

                                        <div id="content" data-aos="fade-up">
        
                                            <div class="table-responsive col-xl">
                                                <!--begin::Table Widget 6-->
                                                <div class="card text-bg-dark mb-3">
                                                    <!--begin::Header-->
                                                    <div class="card-header border-0 pt-5">
                                                        <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                                                            <div class="form-check">
                                                                <input id="checkimage" class="form-check-input" type="checkbox">
                                                                <label class="form-check-label" for="flexCheckDefault">Sin fotos</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Body-->
                                                    <!--begin::Table-->
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                            <div class="table-responsive">        
                                                                <table id="CommoTable" class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%" >
                                                                    <thead class="text-center" style="background-color: #17A2B8;color: white;">
                                                                        <tr>
                                                                            <th>Cod</th>
                                                                            <th>Producto</th>
                                                                            <th>Referencia</th>
                                                                            <th>Marca</th>
                                                                            <th>Disp.</th>
                                                                            <th>Imagen</th>
                                                                            <th>Accion</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>                           
                                                                    </tbody>
                                                                    <tfoot style="background-color: #ccc;color: white;">
                                                                        <tr>
                                                                            <th>Cod</th>
                                                                            <th>Producto</th>
                                                                            <th>Referencia</th>
                                                                            <th>Marca</th>
                                                                            <th>Disp.</th>
                                                                            <th>Imagen</th>
                                                                            <th>Accion</th>
                                                                        </tr>
                                                                    </tfoot>        
                                                                </table>               
                                                            </div>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <!--end::Table-->
                                                    <!--end::Body-->
                                                </div>

                                                <!--end::Tables Widget 6-->
                                            </div>
                                        
                                        </div>





                                        <!--end::Table-->
                                        <!--end::Body-->
                                    </div>

                                    <!--end::Tables Widget 6-->
                                </div>
                            

                            </div>

                        </section>
                    </div>

                </div>

            </div>
            <!-- End Services Section -->         

        </div>
        <?php include 'modals/modals.php' ?>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php"); ?>
        <script type="text/javascript" src="catalogue.js"></script><?php
    }
    ?>
</body>

</html>