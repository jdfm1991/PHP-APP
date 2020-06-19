<!-- MENU SUPERIOR TOP -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo SERVERURL; ?>principal.php" class="nav-link">Home</a>
        </li>
    </ul>
</nav>
<!-- BOX COMPLETO MENU LATERAL -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- LOGO SUPERIOR MENU -->
    <a href="<?php echo SERVERURL; ?>principal.php" class="brand-link">
        <img src="<?php echo SERVERURL; ?>public/dist/img/AdminLTELogo.png " alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Logistica y Despacho</span>
    </a>
    <!-- PERFIL DE USUARIO -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo SERVERURL; ?>public/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION["nomper"]; ?></a>
            </div>
        </div>
        <!-- MENU LATERAL -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- CLIENTES -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Clientes<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>crearcliente/crearcliente.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>relacionclientes/relacionclientes.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Relación</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- DESPACHOS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon  fas fa-truck-loading"></i>
                        <p>Despachos<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>despachos/despachos.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Despacho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Relación de Despachos</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- ESTADISTICAS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>Estadísticas<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <!-- SUB MENU ESTADISTICAS -->
                    <ul class="nav nav-treeview">
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Activaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>clientescodnestle/clientescodnestle.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes COD Nestle</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>activacionclientes/activacionclientes.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Activación de Clientes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>clientesbloqueados/clientesbloqueados.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Bloqueados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>clientessintr/clientessintr.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Sin TR</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>clientesnuevos/clientesnuevos.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Nuevos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>clientesnoactivos/clientesnoactivos.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes no Activos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- ALMACEN -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Almacen<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU ALMACEN -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Indicadores Gestión Desp</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="estadisticas_fact_sin_des.php" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Facturas sin Despachar</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>
                        <!-- COMPRAS -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Compras<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU COMPRAS -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>sellin/sellin.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Sell In</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Reporte de compras</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Historico Costos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Tasa Dolar</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>
                        <!-- CUENTAS POR COBRAR -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cuentas por Cobrar<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU CUENTAS POR COBRAR -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Resumen de Cobranzas por EDV</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Reporte de Comisiones por Edv</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Estado de Cuentas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Facturas Pendientes por Cobrar en Divisas</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>
                        <!-- INVENTARIO -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Inventario
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <!-- SUB MENU INVENTARIO -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>listadeprecio/listadeprecio.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Lista de Precios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>costodeinventario/costodeinventario.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Costos de Inventario</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?php echo SERVERURL; ?>inventarioglobal/inventarioglobal.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Inventario Global</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Sku Pendientes por Facturar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Indicador de d�as de Inventario por SKU</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Inventario en Paquetes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Disponible ALmacen</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- CONFIGURACION -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">

                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Configuración<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>usuarios/usuarios.php" class="nav-link">

                                <i class="fas fa-user-plus nav-icon"></i>
                                <p>Crear Usuario</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>choferes/choferes.php" class="nav-link">

                                <i class="fas fa-walking nav-icon"></i>
                                <p>Crear Chofer</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>vehiculos/vehiculos.php" class="nav-link">

                                <i class="fas fa-truck nav-icon"></i>
                                <p>Crear Vehiculo</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo SERVERURL; ?>roles/roles.php" class="nav-link">

                                <i class="fas fa-user-lock nav-icon"></i>
                                <p>Crear Rol de Usuario</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">

                                <i class="fas fa-mail-bulk nav-icon"></i>
                                <p>Notificaciones</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- CERRAR SESION -->
                <li class="nav-item">
                    <a href="<?php echo SERVERURL; ?>destruir.php" class="nav-link">
                        <i class="fas fa-user-times"></i>
                        <p>Cerrar sesión</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>