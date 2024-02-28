<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR_DCONFISUR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");
require_once("Kpi_Marcas_dos_modelo.php");
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once("../header.php"); ?>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="#" class="navbar-brand">
                    <img src="<?php echo URL_LIBRARY; ?>dist/img/logo_empresa.png " alt="Logo_Empresa"
                         width="200" height="90" style="opacity: .99">
                    <span class="brand-text font-weight-light">
                    DISTRIBUCIONES CONFISUR, C.A
                    </span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <li class="nav-item">
                        <input type="button" class="btn btn-primary" value="Cerrar Ventana" onClick="self.close();"
                            onKeyPress="self.close();" />
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark"> KPI <small>Marcas (New)</small></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">KPI</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <div class="row mb-2">
                        <dt class="col-sm-3 text-gray">Fecha de Consulta:</dt>
                        <?php
                        $mes = $_GET['fecha'];

                        if ($mes == '01') {

                            $string = 'ENERO';

                        } else {

                            if ($mes == '02') {
                                $string = 'FEBRERO';
                            } else {

                                if ($mes == '03') {
                                    $string = 'MARZO';
                                } else {

                                    if ($mes == '04') {
                                        $string = 'ABRIL';
                                    } else {

                                        if ($mes == '05') {
                                            $string = 'MAYO';
                                        } else {

                                            if ($mes == '06') {
                                                $string = 'JUNIO';
                                            } else {

                                                if ($mes == '07') {
                                                    $string = 'JULIO';
                                                } else {

                                                    if ($mes == '08') {
                                                        $string = 'AGOSTO';
                                                    } else {

                                                        if ($mes == '09') {
                                                            $string = 'SEPTIEMBRE';
                                                        } else {

                                                            if ($mes == '10') {
                                                                $string = 'OCTUBRE';
                                                            } else {

                                                                if ($mes == '11') {
                                                                    $string = 'NOVIEMBRE';
                                                                } else {

                                                                    if ($mes == '12') {
                                                                        $string = 'DICIEMBRE';
                                                                    } else {
                                                                        $string = '';
                                                                    }

                                                                }

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }


                        ?>
                        <input type="text" class="form-control-sm col-8 text-center" id="fecha"
                            value="<?php echo $string; ?>" readonly>
                        <input type="hidden" class="form-control-sm col-8 text-center" id="fechaA"
                            value="<?php echo $mes; ?>" readonly>

                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <!--<div class="container">-->
                <table id="tabla"
                    class="table table-sm text-center table-condensed  table-striped table-responsive table-primary"
                    style="width:100%;">
                    <thead style="color: white;">
                        <tr style="background-color: teal" id="cells">
                            <th class="small align-middle">Marcas</th>
                            <th class="small align-middle">Data Entry</th>
                            <?php
                            $modelos = new Kpi_Marcas_dos();
                            $contadorCabecera = 2;

                            $datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor, Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY DataEntry_Vendedores.CodVend desc");
                            $DataEntryPorcentual = $ValorVende = $CodVend = '';
                            foreach ($datos as $row) {

                                $CodVend = ($row["CodVend"]);
                                $ValorVende = number_format($row["Valor"], 0);


                                ?>

                                <th style="border-left-style: double;" class="small align-middle">
                                    <?php echo $CodVend . ' - ' . $ValorVende . ' %'; ?>
                                </th>
                                <th style="width: 15px;">
                                    <div class="small align-middle"
                                        style="width: 70px; word-wrap: break-word; text-align: center">ALCANZADO</div>
                                </th>
                                <th style="width: 15px;">
                                    <div class="small align-middle"
                                        style="width: 70px; word-wrap: break-word; text-align: center">% </div>
                                </th>
                                <th style="width: 15px; border-right-style: double;">
                                    <div class="small align-middle"
                                        style="width: 70px; word-wrap: break-word; text-align: center">CLIENTES ACTIVADOS
                                    </div>
                                </th>
                                <?php $contadorCabecera = $contadorCabecera + 4;
                            } ?>




                        </tr>

                    </thead>
                    <tbody style="background-color: aliceblue">

                        <?php

                        $anno = date('Y');
                        $diasAc = date('d');
                        $mesAc = date('m');
                        $diai = '01';
                        $diaf = '30';

                        if ($mes == '01') {

                            $diaf = '31';

                        } else {

                            if ($mes == '02') {

                                $diaf = '28';

                            } else {

                                if ($mes == '03') {

                                    $diaf = '31';

                                } else {

                                    if ($mes == '05') {
                                        $diaf = '31';

                                    } else {

                                        if ($mes == '07') {
                                            $diaf = '31';

                                        } else {

                                            if ($mes == '08') {
                                                $diaf = '31';

                                            } else {

                                                if ($mes == '10') {
                                                    $diaf = '31';

                                                } else {

                                                    if ($mes == '12') {
                                                        $diaf = '31';

                                                    } else {

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                        $fechai = $anno . '-' . $mes . '-' . $diai;
                            if($mes == $mesAc){
                                $fechaf = $anno . '-' . $mes . '-' . $diasAc;   
                            }else{
                               $fechaf = $anno . '-' . $mes . '-' . $diaf;
                            }
                        /*CAMBIO 1*/
                        //$datos = $modelos->consultaSQL("SELECT count(CodMarca) contador , CodMarca as marca , Valor FROM DataEntry_Marcas  WHERE valor>0  GROUP BY CodMarca,Valor");
                        $datos = $modelos->consultaSQL("SELECT count(CodMarca) contador , CodMarca as marca , Valor , CodInst FROM DataEntry_Marcas inner join SAPROD on SAPROD.Marca = DataEntry_Marcas.CodMarca WHERE valor>0  GROUP BY CodInst, CodMarca,Valor");
                        $contadorMarcas = 0;
                        $marca_array = array();
                        $Valormarca_array = array();

                        /*CAMBIO 7*/
                        $CodInst_array = array();
                        $CalculosAux_array = array();


                        foreach ($datos as $row) {
                            /*CAMBIO 2*/
                            $contadorMarcas += 1; //$row["contador"];
                            array_push($marca_array, $row["marca"]);
                            array_push($Valormarca_array, $row["Valor"]);
                            array_push($CodInst_array, $row["CodInst"]);

                        }


                        $columnas = $contadorCabecera;
                        $filas = $contadorMarcas; // OK?
                        $acumuladorActivados = $acumuladorPorcentual = $acumuladorAlcandado = $acumuladorVendedor = 0;

                        $acumuladorActivadosDTS = $acumuladorPorcentualDTS = $acumuladorAlcandadoDTS = $acumuladorVendedorDTS = 0;
                        $acumuladorActivadosMayor = $acumuladorPorcentualMayor = $acumuladorAlcandadoMayor = $acumuladorVendedorMayor = 0;
                        $acumuladorActivadosOT = $acumuladorPorcentualOT = $acumuladorAlcandadoOT = $acumuladorVendedorOT = 0;
                        $acumuladorActivadosDTA = $acumuladorPorcentualDTA = $acumuladorAlcandadoDTA = $acumuladorVendedorDTA = 0;

                        $repetidor = 0;

                        $Calculos_array = array();


                        for ($k = 0; $k < $columnas; ++$k) {

                            $Calculos_array[0] = "<span style=\"color: #FF0000\">TOTAL GENERAL</span>";
                            $Calculos_array[$k] = 0;

                            $CalculosAux_array[0] = "<span style=\"color: #FF0000\">TOTAL</span>";
                            $CalculosAux_array[$k] = 0;
                        }


                        for ($x = 0; $x < $filas; ++$x) {
                            $Data_array = array();
                            $DataNumerico_array = array();

                            $CodMarca = $marca_array[$x];
                            array_push($Data_array, $CodMarca);

                            $Valor = $Valormarca_array[$x];

                            array_push($Data_array, number_format($Valor, 2));

                            array_push($DataNumerico_array, $Valor);

                            $datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor, Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY DataEntry_Vendedores.CodVend desc");
                            $Vendedores_array = array();

                            foreach ($datos as $row) {
                                $DataEntryPorcentual = 0;
                                $alcanzadoPorcentual = 0;

                                $CodVend = $row["CodVend"];
                                $ValorVende = $row["Valor"];
                                $Clase = $row["Clase"];

                                $DataEntryPorcentual = ($Valor * $ValorVende) / 100;

                                if ($Clase == 'DTS') {

                                    $acumuladorVendedorDTS += $DataEntryPorcentual;

                                } else {
                                    if ($Clase == 'OT') {
                                        $acumuladorVendedorOT += $DataEntryPorcentual;

                                    } else {

                                        if ($Clase == 'MAYOR') {
                                            $acumuladorVendedorMayor += $DataEntryPorcentual;

                                        } else {

                                            if ($Clase == 'DISTRIBUID') {
                                                $acumuladorVendedorDTA += $DataEntryPorcentual;

                                            }

                                        }

                                    }
                                }

                                array_push($Data_array, number_format($DataEntryPorcentual, 2));

                                array_push($DataNumerico_array, $DataEntryPorcentual);

                                //$datosAlcanzados = $modelos->consultaSQL("SELECT TipoFac, CodItem, Cantidad, TotalItem, Tasai, CodVend from SAITEMFAC inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem where SAPROD.Marca LIKE '$CodMarca' and CodVend='$CodVend' and  FechaE between '$fechai' and '$fechaf' and TipoFac in ('A','B','C','D')");

                                $datosAlcanzadosFact = $modelos->consultaSQL("SELECT
                                SAITEMFAC.TipoFac AS TipoFac,
                                SAITEMFAC.CodItem,
                                SAITEMFAC.Cantidad,
                                SAITEMFAC.TotalItem as TotalItem,
                                SAITEMFAC.Descto as descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                                SAITEMFAC.Tasai
                                --(SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS Tasai
                                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between '$fechai' and '$fechaf'  AND saprod.marca LIKE '$CodMarca' AND  SAFACT.codvend LIKE '$CodVend' AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B')");

                                
                                $alcanzadoFact = $alcanzadoNe =$alcanzado = 0;
                                foreach ($datosAlcanzadosFact as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoFact += ((($row3['TotalItem']* $multiplicador) ) / $row3['Tasai']) ;

                                }


                                $datosAlcanzadosNe = $modelos->consultaSQL("SELECT
                                saitemnota.tipofac AS TipoFac,
                                SAITEMNOTA.CodItem,
                                SAITEMNOTA.Cantidad,
                                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS TotalItem,
                               (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS CodVend
                                FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                                INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between '$fechai' and '$fechaf' AND saprod.marca LIKE '$CodMarca' and sanota.codvend LIKE '$CodVend'  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                               SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ");

          
                                foreach ($datosAlcanzadosNe as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoNe += ((($row3['TotalItem']* $multiplicador) )) ;

                                }

                                $alcanzado=$alcanzadoFact+$alcanzadoNe;



                                if ($Clase == 'DTS') {

                                    $acumuladorAlcandadoDTS += $alcanzado;

                                } else {
                                    if ($Clase == 'OT') {
                                        $acumuladorAlcandadoOT += $alcanzado;

                                    } else {

                                        if ($Clase == 'MAYOR') {
                                            $acumuladorAlcandadoMayor += $alcanzado;

                                        } else {

                                            if ($Clase == 'DISTRIBUID') {
                                                $acumuladorAlcandadoDTA += $alcanzado;

                                            }

                                        }

                                    }
                                }

                                array_push($Data_array, number_format($alcanzado, 2));

                                array_push($DataNumerico_array, $alcanzado);

                                if ($alcanzado <= 0) {
                                    $alcanzadoPorcentual = number_format(0, 1);
                                } else {
                                    $alcanzadoPorcentual = number_format((($alcanzado / $DataEntryPorcentual) * 100), 1);
                                }


                                if ($Clase == 'DTS') {

                                    $PorcentualDTS = number_format((($acumuladorAlcandadoDTS / $acumuladorVendedorDTS) * 100), 1);

                                    if ($PorcentualDTS >= 0 and $PorcentualDTS <= 50) {
                                        $validador = "bg-danger color-palette";
                                    } else {
                                        if ($PorcentualDTS >= 51 and $PorcentualDTS <= 80) {
                                            $validador = "bg-warning color-palette";
                                        } else {
                                            if ($PorcentualDTS >= 81) {
                                                $validador = "bg-success color-palette";
                                            }
                                        }
                                    }

                                    $validadorValorDTS = '<div class=' . $validador . ' ><span> ' . $PorcentualDTS . ' </span></div>';

                                } else {
                                    if ($Clase == 'OT') {
                                        $PorcentualOT = number_format((($acumuladorAlcandadoOT / $acumuladorVendedorOT) * 100), 1);

                                        if ($PorcentualOT >= 0 and $PorcentualOT <= 50) {
                                            $validador = "bg-danger color-palette";
                                        } else {
                                            if ($PorcentualOT >= 51 and $PorcentualOT <= 80) {
                                                $validador = "bg-warning color-palette";
                                            } else {
                                                if ($PorcentualOT >= 81) {
                                                    $validador = "bg-success color-palette";
                                                }
                                            }
                                        }

                                        $validadorValorOT = '<div class=' . $validador . ' ><span> ' . $PorcentualOT . ' </span></div>';

                                    } else {

                                        if ($Clase == 'MAYOR') {
                                            $PorcentualMayor = number_format((($acumuladorAlcandadoMayor / $acumuladorVendedorMayor) * 100), 1);

                                            if ($PorcentualMayor >= 0 and $PorcentualMayor <= 50) {
                                                $validador = "bg-danger color-palette";
                                            } else {
                                                if ($PorcentualMayor >= 51 and $PorcentualMayor <= 80) {
                                                    $validador = "bg-warning color-palette";
                                                } else {
                                                    if ($PorcentualMayor >= 81) {
                                                        $validador = "bg-success color-palette";
                                                    }
                                                }
                                            }

                                            $validadorValorMayor = '<div class=' . $validador . ' ><span> ' . $PorcentualMayor . ' </span></div>';

                                        } else {

                                            if ($Clase == 'DISTRIBUID') {
                                                $PorcentualDTA = number_format((($acumuladorAlcandadoDTA / $acumuladorVendedorDTA) * 100), 1);

                                                if ($PorcentualDTA >= 0 and $PorcentualDTA <= 50) {
                                                    $validador = "bg-danger color-palette";
                                                } else {
                                                    if ($PorcentualDTA >= 51 and $PorcentualDTA <= 80) {
                                                        $validador = "bg-warning color-palette";
                                                    } else {
                                                        if ($PorcentualDTA >= 81) {
                                                            $validador = "bg-success color-palette";
                                                        }
                                                    }
                                                }

                                                $validadorValorDTA = '<div class=' . $validador . ' ><span> ' . $PorcentualDTA . ' </span></div>';

                                            }

                                        }

                                    }
                                }


                                if ($alcanzadoPorcentual >= 0 and $alcanzadoPorcentual <= 50) {

                                    $validador = "bg-danger color-palette";

                                } else {

                                    if ($alcanzadoPorcentual >= 51 and $alcanzadoPorcentual <= 80) {

                                        $validador = "bg-warning color-palette";

                                    } else {

                                        if ($alcanzadoPorcentual >= 81) {
                                            $validador = "bg-success color-palette";
                                        }
                                    }

                                }

                                $validadorValor = '<div class=' . $validador . ' ><span> ' . $alcanzadoPorcentual . ' </span></div>';

                                array_push($Data_array, $validadorValor);


                                if ($alcanzado <= 0) {
                                    array_push($DataNumerico_array, (0));
                                } else {
                                    array_push($DataNumerico_array, (($alcanzado / $DataEntryPorcentual) * 100));
                                }

                                $datosActivaciones = $modelos->consultaSQL("SELECT distinct(SAFACT.CodClie) AS codclie, Descrip as descrip, Direc2 AS direc, (SELECT DiasVisitas FROM SACLIE_01 WHERE SACLIE_01.CodClie=SAFACT.CodClie) as dia_visita FROM SAFACT WHERE SAFACT.CodVend = '$CodVend' AND TipoFac in ('A') AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie inner join SAITEMFAC on SAFACT.NumeroD = SAITEMFAC.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem 
                                WHERE SAPROD.Marca LIKE '$CodMarca' and SACLIE.Activo = '1' AND (SACLIE.CodVend = '$CodVend')) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) between '$fechai' and '$fechaf' AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac in ('A') AND x.NumeroR is not NULL AND cast(X.Monto as BIGINT) = cast((select Z.Monto from SAFACT AS Z where Z.NumeroD = x.NumeroR and Z.TipoFac in ('B'))as BIGINT)) 
                                UNION
                                SELECT distinct(SANOTA.CodClie) AS codclie, rsocial AS descip, direccion AS direc, (SELECT DiasVisitas FROM SACLIE_01 WHERE SACLIE_01.CodClie=SANOTA.CodClie) as dia_visita FROM SANOTA WHERE SANOTA.CodVend = '$CodVend' AND TipoFac in ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie inner join SAITEMNOTA on SANOTA.NumeroD = SAITEMNOTA.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMNOTA.CodItem
                                WHERE SAPROD.Marca LIKE '$CodMarca' and SACLIE.Activo = '1' AND (SACLIE.CodVend = '$CodVend')) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) between '$fechai' and '$fechaf' AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac in ('C') AND x.numerof is not NULL AND cast(X.subtotal as BIGINT) = cast((select Z.subtotal from SANOTA AS Z where Z.NumeroD = x.numerof and Z.TipoFac in ('D'))as BIGINT))");
                                
                                $activaciones = 0;

                                foreach ($datosActivaciones as $row4) {
                                  //  $activaciones = $row4['contador'];

                                  $activaciones = $activaciones +1;

                                }

                                if ($Clase == 'DTS') {

                                    $acumuladorActivadosDTS += $activaciones;

                                } else {
                                    if ($Clase == 'OT') {
                                        $acumuladorActivadosOT += $activaciones;

                                    } else {

                                        if ($Clase == 'MAYOR') {
                                            $acumuladorActivadosMayor += $activaciones;

                                        } else {

                                            if ($Clase == 'DISTRIBUID') {
                                                $acumuladorActivadosDTA += $activaciones;

                                            }

                                        }

                                    }
                                }

                                array_push($Data_array, $activaciones);

                                array_push($DataNumerico_array, $activaciones);

                            }


                            /*CAMBIO 5*/
                            $masCuatro = 2;
                            $masCinco = 4;
                            echo '<tr>';
                            if ($CodInst_array[$x] != 809 and $repetidor == 0) {
                                for ($y = 0; $y < $columnas; ++$y) {
                                    // ESCRIBE LA FILA DE PEPSICO
                                   /* $calculo = 0;

                                   

                                    if ($y == 0) {    
                                        echo "<span style=\"color: #FF0000\">TOTAL PEPSICO</span>";  
                                    } else {

                                        $ValorNumerico = number_format($Calculos_array[$y], 2);

                                        if ($y == $masCinco) {
                                            if ($Calculos_array[$y - 2] <= 0) {
                                                $ValorNumerico = number_format(0, 2);
                                            } else {
                                                $calculo = ($Calculos_array[$y - 1] / $Calculos_array[$y - 2]) * 100;
                                                $ValorNumerico = number_format($calculo, 2);
                                            }
                                            $masCinco = $masCinco + 4;

                                            if ($calculo >= 0 and $calculo <= 50) {

                                                $validador = "bg-danger color-palette";

                                            } else {

                                                if ($calculo >= 51 and $calculo <= 80) {

                                                    $validador = "bg-warning color-palette";

                                                } else {

                                                    if ($calculo >= 81) {
                                                        $validador = "bg-success color-palette";

                                                    } else {

                                                        if ($calculo < 0) {
                                                            $validador = "bg-danger color-palette";
                                                        }
                                                    }
                                                }

                                            }

                                            $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                            // rutinas....
                                            echo "$validadorValor";

                                        } else {

                                            // rutinas....
                                            echo "$ValorNumerico";
                                        }


                                    }
                                    echo '</td>';*/

                                    $repetidor += 1;
                                }
                            }
                            echo '</tr>';
                            /* FIN CAMBIO 5*/

                            $Calculos_array[0] = "<span style=\"color: #FF0000\">TOTAL GENERAL</span>";
                            $CalculosAux_array[0] = "<span style=\"color: #FF0000\">TOTAL</span>";

                            // "vector" X
                            echo '<tr>';
                            $masCuatro = 2;
                            for ($y = 0; $y < $columnas; ++$y) {

                                if ($y == $masCuatro) {
                                    echo '<td style="width: 15px; border-left-style: double;">';
                                    $masCuatro = $masCuatro + 4;
                                } else {

                                    echo '<td>';

                                }

                                // rutinas....
                                echo "$Data_array[$y]";

                                if ($y > 0) {
                                    $Calculos_array[$y] += $DataNumerico_array[$y - 1];

                                    if ($CodInst_array[$x] != 809) {
                                        $CalculosAux_array[$y] += $DataNumerico_array[$y - 1];
                                    }
                                }

                                echo '</td>';


                            }
                            // cerramos X
                            echo '</tr>';
                           
                        }
                        /*CAMBIO 8 TOTAL*/
                        echo '<tr>';
                        $masCuatro = 2;
                        $masCinco = 4;
                        for ($s = 0; $s < $columnas; ++$s) {
                            if ($s == 0) {

                                echo '<td>';
                                // rutinas....
                                echo "$CalculosAux_array[$s]";
                                echo '</td>';


                            } else {

                                $ValorNumerico = number_format($CalculosAux_array[$s], 2);

                                if ($s == $masCuatro) {
                                    echo '<td style="width: 15px; border-left-style: double;">';
                                    $masCuatro = $masCuatro + 4;
                                } else {

                                    echo '<td>';

                                }


                                if ($s == $masCinco) {
                                    if ($CalculosAux_array[$s - 2] <= 0) {
                                        $ValorNumerico = number_format(0, 2);
                                    } else {
                                        $calculo = ($CalculosAux_array[$s - 1] / $CalculosAux_array[$s - 2]) * 100;
                                        $ValorNumerico = number_format($calculo, 2);
                                    }
                                    $masCinco = $masCinco + 4;

                                    if ($calculo >= 0 and $calculo <= 50) {

                                        $validador = "bg-danger color-palette";

                                    } else {

                                        if ($calculo >= 51 and $calculo <= 80) {

                                            $validador = "bg-warning color-palette";

                                        } else {

                                            if ($calculo >= 81) {
                                                $validador = "bg-success color-palette";

                                            } else {

                                                if ($calculo < 0) {
                                                    $validador = "bg-danger color-palette";
                                                }
                                            }
                                        }

                                    }

                                    $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                    // rutinas....
                                    echo "$validadorValor";

                                } else {

                                    // rutinas....
                                    echo "$ValorNumerico";
                                }

                                echo '</td>';


                            }

                        }
                        // cerramos X
                        echo '</tr>';


                        /*TOTAL GENERAL*/
                        echo '<tr>';
                        $masCuatro = 2;
                        $masCinco = 4;
                        for ($s = 0; $s < $columnas; ++$s) {
                            if ($s == 0) {

                                echo '<td>';
                                // rutinas....
                                echo "$Calculos_array[$s]";
                                echo '</td>';


                            } else {

                                $ValorNumerico = number_format($Calculos_array[$s], 2);

                                if ($s == $masCuatro) {
                                    echo '<td style="width: 15px; border-left-style: double;">';
                                    $masCuatro = $masCuatro + 4;
                                } else {

                                    echo '<td>';

                                }


                                if ($s == $masCinco) {
                                    if ($Calculos_array[$s - 2] <= 0) {
                                        $ValorNumerico = number_format(0, 2);
                                    } else {
                                        $calculo = ($Calculos_array[$s - 1] / $Calculos_array[$s - 2]) * 100;
                                        $ValorNumerico = number_format($calculo, 2);
                                    }
                                    $masCinco = $masCinco + 4;

                                    if ($calculo >= 0 and $calculo <= 50) {

                                        $validador = "bg-danger color-palette";

                                    } else {

                                        if ($calculo >= 51 and $calculo <= 80) {

                                            $validador = "bg-warning color-palette";

                                        } else {

                                            if ($calculo >= 81) {
                                                $validador = "bg-success color-palette";

                                            } else {

                                                if ($calculo < 0) {
                                                    $validador = "bg-danger color-palette";
                                                }
                                            }
                                        }

                                    }

                                    $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                    // rutinas....
                                    echo "$validadorValor";

                                } else {

                                    // rutinas....
                                    echo "$ValorNumerico";
                                }

                                echo '</td>';


                            }

                        }
                        // cerramos X
                        echo '</tr>';

                        echo '</tbody>';
                        echo '</table>';
                        ?>

                        <hr>
                        <h2 class="m-0 text-dark"> Tabla General - <small>Canales de ventas</small></h2>
                        <br>


                        <table id="tabla1"
                            class="table table-sm text-center table-condensed  table-striped table-responsive table-primary"
                            style="width:100%;">
                            <thead style="color: white;">
                                <tr style="background-color: teal" id="cells">
                                    <th class="small align-middle">Marcas</th>
                                    <th class="small align-middle">Data Entry</th>
                                    <?php
                                    $modelos = new Kpi_Marcas_dos();
                                    $contadorCabecera = 2;

                                    $datos = $modelos->consultaSQL("SELECT distinct Clase  FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY Clase desc");
                                    $ClaseAux = $Clase =$DataEntryPorcentual = $ValorVende = $CodVend = '';
                                    foreach ($datos as $row) {

                                        $Clase = ($row["Clase"]);

                                        ?>

                                        <th style="border-left-style: double;" class="small align-middle">
                                            <?php echo $Clase; ?>
                                        </th>
                                        <th style="width: 15px;">
                                            <div class="small align-middle"
                                                style="width: 70px; word-wrap: break-word; text-align: center">ALCANZADO
                                            </div>
                                        </th>
                                        <th style="width: 15px;">
                                            <div class="small align-middle"
                                                style="width: 70px; word-wrap: break-word; text-align: center">% </div>
                                        </th>
                                        <th style="width: 15px; border-right-style: double;">
                                            <div class="small align-middle"
                                                style="width: 70px; word-wrap: break-word; text-align: center">CLIENTES
                                                ACTIVADOS</div>
                                        </th>
                                        <?php $contadorCabecera = $contadorCabecera + 4;
                                    } ?>




                                </tr>

                            </thead>
                            <tbody style="background-color: aliceblue">

                                <?php

                                $anno = date('Y');
                                $diasAc = date('d');
                                $mesAc = date('m');
                                $diai = '01';
                                $diaf = '30';

                                if ($mes == '01') {

                                    $diaf = '31';

                                } else {

                                    if ($mes == '02') {

                                        $diaf = '28';

                                    } else {

                                        if ($mes == '03') {

                                            $diaf = '31';

                                        } else {

                                            if ($mes == '05') {
                                                $diaf = '31';

                                            } else {

                                                if ($mes == '07') {
                                                    $diaf = '31';

                                                } else {

                                                    if ($mes == '08') {
                                                        $diaf = '31';

                                                    } else {

                                                        if ($mes == '10') {
                                                            $diaf = '31';

                                                        } else {

                                                            if ($mes == '12') {
                                                                $diaf = '31';

                                                            } else {

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                                $fechai = $anno . '-' . $mes . '-' . $diai;
                                    if($mes == $mesAc){
                                        $fechaf = $anno . '-' . $mes . '-' . $diasAc;   
                                    }else{
                                    $fechaf = $anno . '-' . $mes . '-' . $diaf;
                                    }
                                /*CAMBIO 3*/
                                $datos = $modelos->consultaSQL("SELECT count(CodMarca) contador , CodMarca as marca , Valor , CodInst FROM DataEntry_Marcas inner join SAPROD on SAPROD.Marca = DataEntry_Marcas.CodMarca WHERE valor>0  GROUP BY CodInst, CodMarca,Valor");
                                $contadorMarcas = 0;
                                $marca_array = array();
                                $Valormarca_array = array();


                                $CodInst_array = array();
                                $CalculosAux_array = array();


                                foreach ($datos as $row) {
                                    /*CAMBIO 4*/
                                    $contadorMarcas += 1;
                                    array_push($marca_array, $row["marca"]);
                                    array_push($Valormarca_array, $row["Valor"]);
                                    array_push($CodInst_array, $row["CodInst"]);

                                }


                                $columnas = $contadorCabecera;
                                $filas = $contadorMarcas; // OK?
                                $acumuladorActivados = $acumuladorPorcentual = $acumuladorAlcandado = $acumuladorVendedor = 0;


                                $validadorValorMayor = $validadorValorOT = $validadorValorDTA = $validadorValorDTS = '';
                                $validadorValorMayornumerio = $validadorValorOTnumerio = $validadorValorDTAnumerio = $validadorValorDTSnumerio = 0;
                                $validadorValorDTS = '';
                                $repetidor = 0;

                                $Calculos_array = array();


                                for ($k = 0; $k < $columnas; ++$k) {

                                    $Calculos_array[0] = "<span style=\"color: #FF0000\">TOTAL GENERAL</span>";
                                    $Calculos_array[$k] = 0;

                                    $CalculosAux_array[0] = "<span style=\"color: #FF0000\">TOTAL</span>";
                                    $CalculosAux_array[$k] = 0;
                                }


                                for ($x = 0; $x < $filas; ++$x) {

                                    $acumuladorActivadosDTS = $acumuladorPorcentualDTS = $acumuladorAlcandadoDTS = $acumuladorVendedorDTS = 0;
                                    $acumuladorActivadosMayor = $acumuladorPorcentualMayor = $acumuladorAlcandadoMayor = $acumuladorVendedorMayor = 0;
                                    $acumuladorActivadosOT = $acumuladorPorcentualOT = $acumuladorAlcandadoOT = $acumuladorVendedorOT = 0;
                                    $acumuladorActivadosDTA = $acumuladorPorcentualDTA = $acumuladorAlcandadoDTA = $acumuladorVendedorDTA = 0;

                                    $Data_array = array();
                                    $DataNumerico_array = array();

                                    $CodMarca = $marca_array[$x];
                                    array_push($Data_array, $CodMarca);

                                    $Valor = $Valormarca_array[$x];

                                    array_push($Data_array, number_format($Valor, 2));

                                    array_push($DataNumerico_array, $Valor);

                                    $datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor  FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY DataEntry_Vendedores.CodVend desc");
                                    $Vendedores_array = array();
                                    $Clase = '';
                                    foreach ($datos as $row) {
                                        $DataEntryPorcentual = 0;
                                        $alcanzadoPorcentual = 0;

                                        $CodVend = $row["CodVend"];
                                        $ValorVende = $row["Valor"];
                                        
                                        $datosAux = $modelos->consultaSQL("SELECT Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  and SAVEND.CodVend='$CodVend'");
                                        foreach ($datosAux as $rowAux) {
                                        $Clase = $rowAux["Clase"];
                                        }

                                        $DataEntryPorcentual = ($Valor * $ValorVende) / 100;

                                        if ($Clase == 'DTS') {

                                            $acumuladorVendedorDTS += $DataEntryPorcentual;

                                        } else {
                                            if ($Clase == 'OT') {
                                                $acumuladorVendedorOT += $DataEntryPorcentual;

                                            } else {

                                                if ($Clase == 'MAYOR') {
                                                    $acumuladorVendedorMayor += $DataEntryPorcentual;

                                                } else {

                                                    if ($Clase == 'DISTRIBUID') {
                                                        $acumuladorVendedorDTA += $DataEntryPorcentual;

                                                    }

                                                }

                                            }
                                        }


                                       // $datosAlcanzados = $modelos->consultaSQL("SELECT TipoFac, CodItem, Cantidad, TotalItem, Tasai, CodVend from SAITEMFAC inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem where SAPROD.Marca LIKE '$CodMarca' and CodVend='$CodVend' and  FechaE between '$fechai' and '$fechaf' and TipoFac in ('A','B','C','D')");
                                    

                                            $datosAlcanzadosFact = $modelos->consultaSQL("SELECT
                                            SAITEMFAC.TipoFac AS TipoFac,
                                            SAITEMFAC.CodItem,
                                            SAITEMFAC.Cantidad,
                                            SAITEMFAC.TotalItem as TotalItem,
                                            SAITEMFAC.Descto as descuento,
                                            (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                                            SAITEMFAC.Tasai
                                            --(SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS Tasai
                                            FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                                            INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                                            DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between '$fechai' and '$fechaf'  AND saprod.marca LIKE '$CodMarca' AND  SAFACT.codvend LIKE '$CodVend' AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B')");

                                            
                                            $alcanzadoFact = $alcanzadoNe =$alcanzado = 0;
                                            foreach ($datosAlcanzadosFact as $row3) {

                                                if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                                    $multiplicador = -1;
                                                } else {
                                                    $multiplicador = 1;
                                                }

                                                $alcanzadoFact += ((($row3['TotalItem']* $multiplicador) ) / $row3['Tasai']) ;

                                            }


                                            $datosAlcanzadosNe = $modelos->consultaSQL("SELECT
                                            saitemnota.tipofac AS TipoFac,
                                            SAITEMNOTA.CodItem,
                                            SAITEMNOTA.Cantidad,
                                            (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS TotalItem,
                                        (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                                            (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS CodVend
                                            FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                                            INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                                            DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between '$fechai' and '$fechaf' AND saprod.marca LIKE '$CodMarca' and sanota.codvend LIKE '$CodVend'  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                                            SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ");

                    
                                            foreach ($datosAlcanzadosNe as $row3) {

                                                if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                                    $multiplicador = -1;
                                                } else {
                                                    $multiplicador = 1;
                                                }

                                                $alcanzadoNe += ((($row3['TotalItem']* $multiplicador) )) ;

                                            }

                                            $alcanzado=$alcanzadoFact+$alcanzadoNe;



                                        if ($Clase == 'DTS') {

                                            $acumuladorAlcandadoDTS += $alcanzado;

                                        } else {
                                            if ($Clase == 'OT') {
                                                
                                                $acumuladorAlcandadoOT += $alcanzado;

                                            } else {

                                                if ($Clase == 'MAYOR') {
                                                    $acumuladorAlcandadoMayor += $alcanzado;

                                                } else {

                                                    if ($Clase == 'DISTRIBUID') {
                                                        
                                                        $acumuladorAlcandadoDTA += $alcanzado;

                                                    }

                                                }

                                            }
                                        }


                                        if ($Clase == 'DTS') {

                                            $PorcentualDTS = number_format((($acumuladorAlcandadoDTS / $acumuladorVendedorDTS) * 100), 1);

                                            if ($PorcentualDTS >= 0 and $PorcentualDTS <= 50) {
                                                $validador = "bg-danger color-palette";
                                            } else {
                                                if ($PorcentualDTS >= 51 and $PorcentualDTS <= 80) {
                                                    $validador = "bg-warning color-palette";
                                                } else {
                                                    if ($PorcentualDTS >= 81) {
                                                        $validador = "bg-success color-palette";
                                                    }
                                                }
                                            }

                                            $validadorValorDTS = '<div class=' . $validador . ' ><span> ' . $PorcentualDTS . ' </span></div>';

                                            $validadorValorDTSnumerio = (($acumuladorAlcandadoDTS / $acumuladorVendedorDTS) * 100);

                                        } else {
                                            if ($Clase == 'OT') {
                                                $PorcentualOT = number_format((($acumuladorAlcandadoOT / $acumuladorVendedorOT) * 100), 1);

                                                if ($PorcentualOT >= 0 and $PorcentualOT <= 50) {
                                                    $validador = "bg-danger color-palette";
                                                } else {
                                                    if ($PorcentualOT >= 51 and $PorcentualOT <= 80) {
                                                        $validador = "bg-warning color-palette";
                                                    } else {
                                                        if ($PorcentualOT >= 81) {
                                                            $validador = "bg-success color-palette";
                                                        }
                                                    }
                                                }

                                                $validadorValorOT = '<div class=' . $validador . ' ><span> ' . $PorcentualOT . ' </span></div>';

                                                $validadorValorOTnumerio = ((($acumuladorAlcandadoOT / $acumuladorVendedorOT) * 100));

                                            } else {

                                                if ($Clase == 'MAYOR') {
                                                    $PorcentualMayor = number_format((($acumuladorAlcandadoMayor / $acumuladorVendedorMayor) * 100), 1);

                                                    if ($PorcentualMayor >= 0 and $PorcentualMayor <= 50) {
                                                        $validador = "bg-danger color-palette";
                                                    } else {
                                                        if ($PorcentualMayor >= 51 and $PorcentualMayor <= 80) {
                                                            $validador = "bg-warning color-palette";
                                                        } else {
                                                            if ($PorcentualMayor >= 81) {
                                                                $validador = "bg-success color-palette";
                                                            }
                                                        }
                                                    }

                                                    $validadorValorMayor = '<div class=' . $validador . ' ><span> ' . $PorcentualMayor . ' </span></div>';

                                                    $validadorValorMayornumerio = ((($acumuladorAlcandadoMayor / $acumuladorVendedorMayor) * 100));

                                                } else {

                                                    if ($Clase == 'DISTRIBUID') {
                                                        $PorcentualDTA = number_format((($acumuladorAlcandadoDTA / $acumuladorVendedorDTA) * 100), 1);

                                                        if ($PorcentualDTA >= 0 and $PorcentualDTA <= 50) {
                                                            $validador = "bg-danger color-palette";
                                                        } else {
                                                            if ($PorcentualDTA >= 51 and $PorcentualDTA <= 80) {
                                                                $validador = "bg-warning color-palette";
                                                            } else {
                                                                if ($PorcentualDTA >= 81) {
                                                                    $validador = "bg-success color-palette";
                                                                }
                                                            }
                                                        }

                                                        $validadorValorDTA = '<div class=' . $validador . ' ><span> ' . $PorcentualDTA . ' </span></div>';

                                                        $validadorValorDTAnumerio = ((($acumuladorAlcandadoDTA / $acumuladorVendedorDTA) * 100));

                                                    }

                                                }

                                            }
                                        }


                                        if ($alcanzadoPorcentual >= 0 and $alcanzadoPorcentual <= 50) {

                                            $validador = "bg-danger color-palette";

                                        } else {

                                            if ($alcanzadoPorcentual >= 51 and $alcanzadoPorcentual <= 80) {

                                                $validador = "bg-warning color-palette";

                                            } else {

                                                if ($alcanzadoPorcentual >= 81) {
                                                    $validador = "bg-success color-palette";
                                                }
                                            }

                                        }

                                        $validadorValor = '<div class=' . $validador . ' ><span> ' . $alcanzadoPorcentual . ' </span></div>';

                                        $datosActivaciones = $modelos->consultaSQL("SELECT distinct(SAFACT.CodClie) AS codclie, Descrip as descrip, Direc2 AS direc, (SELECT DiasVisitas FROM SACLIE_01 WHERE SACLIE_01.CodClie=SAFACT.CodClie) as dia_visita FROM SAFACT WHERE SAFACT.CodVend = '$CodVend' AND TipoFac in ('A') AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie inner join SAITEMFAC on SAFACT.NumeroD = SAITEMFAC.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem 
                                        WHERE SAPROD.Marca LIKE '$CodMarca' and SACLIE.Activo = '1' AND (SACLIE.CodVend = '$CodVend')) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) between '$fechai' and '$fechaf' AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac in ('A') AND x.NumeroR is not NULL AND cast(X.Monto as BIGINT) = cast((select Z.Monto from SAFACT AS Z where Z.NumeroD = x.NumeroR and Z.TipoFac in ('B'))as BIGINT)) 
                                        UNION
                                        SELECT distinct(SANOTA.CodClie) AS codclie, rsocial AS descip, direccion AS direc, (SELECT DiasVisitas FROM SACLIE_01 WHERE SACLIE_01.CodClie=SANOTA.CodClie) as dia_visita FROM SANOTA WHERE SANOTA.CodVend = '$CodVend' AND TipoFac in ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie inner join SAITEMNOTA on SANOTA.NumeroD = SAITEMNOTA.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMNOTA.CodItem
                                        WHERE SAPROD.Marca LIKE '$CodMarca' and SACLIE.Activo = '1' AND (SACLIE.CodVend = '$CodVend')) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) between '$fechai' and '$fechaf' AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac in ('C') AND x.numerof is not NULL AND cast(X.subtotal as BIGINT) = cast((select Z.subtotal from SANOTA AS Z where Z.NumeroD = x.numerof and Z.TipoFac in ('D'))as BIGINT))");
                                        
                                        $activaciones = 0;
        
                                        foreach ($datosActivaciones as $row4) {
                                          //  $activaciones = $row4['contador'];
        
                                          $activaciones = $activaciones +1;
        
                                        }

                                        if ($Clase == 'DTS') {

                                            $acumuladorActivadosDTS += $activaciones;

                                        } else {
                                            if ($Clase == 'OT') {
                                                $acumuladorActivadosOT += $activaciones;

                                            } else {

                                                if ($Clase == 'MAYOR') {
                                                    $acumuladorActivadosMayor += $activaciones;

                                                } else {

                                                    if ($Clase == 'DISTRIBUID') {
                                                        $acumuladorActivadosDTA += $activaciones;

                                                    }

                                                }

                                            }
                                        }

                                    }

                                    /*ARRAY OT*/
                                    array_push($Data_array, number_format($acumuladorVendedorOT, 2));
                                    array_push($Data_array, number_format($acumuladorAlcandadoOT, 2));
                                    array_push($Data_array, ($validadorValorOT));
                                    array_push($Data_array, number_format($acumuladorActivadosOT, 2));

                                    array_push($DataNumerico_array, $acumuladorVendedorOT);
                                    array_push($DataNumerico_array, $acumuladorAlcandadoOT);
                                    array_push($DataNumerico_array, $validadorValorOTnumerio);
                                    array_push($DataNumerico_array, $acumuladorActivadosOT);

                                    /*ARRAY MAYOR*/
                                    array_push($Data_array, number_format($acumuladorVendedorMayor, 2));
                                    array_push($Data_array, number_format($acumuladorAlcandadoMayor, 2));
                                    array_push($Data_array, $validadorValorMayor);
                                    array_push($Data_array, number_format($acumuladorActivadosMayor, 2));

                                    array_push($DataNumerico_array, $acumuladorVendedorMayor);
                                    array_push($DataNumerico_array, $acumuladorAlcandadoMayor);
                                    array_push($DataNumerico_array, $validadorValorMayornumerio);
                                    array_push($DataNumerico_array, $acumuladorActivadosMayor);


                                    /*ARRAY DTS*/
                                    array_push($Data_array, number_format($acumuladorVendedorDTS, 2));
                                    array_push($Data_array, number_format($acumuladorAlcandadoDTS, 2));
                                    array_push($Data_array, $validadorValorDTS);
                                    array_push($Data_array, number_format($acumuladorActivadosDTS, 2));

                                    array_push($DataNumerico_array, $acumuladorVendedorDTS);
                                    array_push($DataNumerico_array, $acumuladorAlcandadoDTS);
                                    array_push($DataNumerico_array, $validadorValorDTSnumerio);
                                    array_push($DataNumerico_array, $acumuladorActivadosDTS);

                                    /*ARRAY DISTRIBUIDORA*/
                                    array_push($Data_array, number_format($acumuladorVendedorDTA, 2));
                                    array_push($Data_array, number_format($acumuladorAlcandadoDTA, 2));
                                    array_push($Data_array, $validadorValorDTA);
                                    array_push($Data_array, number_format($acumuladorActivadosDTA, 2));

                                    array_push($DataNumerico_array, $acumuladorVendedorDTA);
                                    array_push($DataNumerico_array, $acumuladorAlcandadoDTA);
                                    array_push($DataNumerico_array, $validadorValorDTAnumerio);
                                    array_push($DataNumerico_array, $acumuladorActivadosDTA);


                                    /*CAMBIO 9*/
                                    $masCuatro = 2;
                                    $masCinco = 4;
                                    echo '<tr>';
                                    if ($CodInst_array[$x] != 809 and $repetidor == 0) {
                                        for ($y = 0; $y < $columnas; ++$y) {
                                            // ESCRIBE LA FILA DE PEPSICO
                                            /*
                                            $calculo = 0;

                                            if ($y == $masCuatro) {
                                                echo '<td style="width: 15px; border-left-style: double;">';
                                                $masCuatro = $masCuatro + 4;
                                            } else {

                                                echo '<td>';

                                            }

                                            if ($y == 0) {
                                                echo "<span style=\"color: #FF0000\">TOTAL PEPSICO</span>";
                                            } else {

                                                $ValorNumerico = number_format($Calculos_array[$y], 2);

                                                if ($y == $masCinco) {
                                                    if ($Calculos_array[$y - 2] <= 0) {
                                                        $ValorNumerico = number_format(0, 2);
                                                    } else {
                                                        $calculo = ($Calculos_array[$y - 1] / $Calculos_array[$y - 2]) * 100;
                                                        $ValorNumerico = number_format($calculo, 2);
                                                    }
                                                    $masCinco = $masCinco + 4;

                                                    if ($calculo >= 0 and $calculo <= 50) {

                                                        $validador = "bg-danger color-palette";

                                                    } else {

                                                        if ($calculo >= 51 and $calculo <= 80) {

                                                            $validador = "bg-warning color-palette";

                                                        } else {

                                                            if ($calculo >= 81) {
                                                                $validador = "bg-success color-palette";

                                                            } else {

                                                                if ($calculo < 0) {
                                                                    $validador = "bg-danger color-palette";
                                                                }
                                                            }
                                                        }

                                                    }

                                                    $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                                    // rutinas....
                                                    echo "$validadorValor";

                                                } else {

                                                    // rutinas....
                                                    echo "$ValorNumerico";
                                                }


                                            }
                                            echo '</td>';*/

                                            $repetidor += 1;
                                        }
                                    }
                                    echo '</tr>';
                                    /* FIN CAMBIO 9*/

                                    $Calculos_array[0] = "<span style=\"color: #FF0000\">TOTAL GENERAL</span>";
                                    $CalculosAux_array[0] = "<span style=\"color: #FF0000\">TOTAL</span>";

                                    // "vector" X
                                    //var_dump($Data_array);
                                    echo '<tr>';
                                    $masCuatro = 2;
                                    for ($y = 0; $y < $columnas; ++$y) {

                                        if ($y == $masCuatro) {
                                            echo '<td style="width: 15px; border-left-style: double;">';
                                            $masCuatro = $masCuatro + 4;
                                        } else {

                                            echo '<td>';

                                        }

                                        // rutinas....
                                        echo "$Data_array[$y]";

                                        if ($y > 0) {
                                            $Calculos_array[$y] += $DataNumerico_array[$y - 1];

                                            if ($CodInst_array[$x] != 809) {
                                                $CalculosAux_array[$y] += $DataNumerico_array[$y - 1];
                                            }
                                        }

                                        echo '</td>';


                                    }
                                    // cerramos X
                                    echo '</tr>';
                                }




                                /*CAMBIO 8 TOTAL*/
                                echo '<tr>';
                                $masCuatro = 2;
                                $masCinco = 4;
                                for ($s = 0; $s < $columnas; ++$s) {
                                    if ($s == 0) {

                                        echo '<td>';
                                        // rutinas....
                                        echo "$CalculosAux_array[$s]";
                                        echo '</td>';


                                    } else {

                                        $ValorNumerico = number_format($CalculosAux_array[$s], 2);

                                        if ($s == $masCuatro) {
                                            echo '<td style="width: 15px; border-left-style: double;">';
                                            $masCuatro = $masCuatro + 4;
                                        } else {

                                            echo '<td>';

                                        }


                                        if ($s == $masCinco) {
                                            if ($CalculosAux_array[$s - 2] <= 0) {
                                                $ValorNumerico = number_format(0, 2);
                                            } else {
                                                $calculo = ($CalculosAux_array[$s - 1] / $CalculosAux_array[$s - 2]) * 100;
                                                $ValorNumerico = number_format($calculo, 2);
                                            }
                                            $masCinco = $masCinco + 4;

                                            if ($calculo >= 0 and $calculo <= 50) {

                                                $validador = "bg-danger color-palette";

                                            } else {

                                                if ($calculo >= 51 and $calculo <= 80) {

                                                    $validador = "bg-warning color-palette";

                                                } else {

                                                    if ($calculo >= 81) {
                                                        $validador = "bg-success color-palette";

                                                    } else {

                                                        if ($calculo < 0) {
                                                            $validador = "bg-danger color-palette";
                                                        }
                                                    }
                                                }

                                            }

                                            $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                            // rutinas....
                                            echo "$validadorValor";

                                        } else {

                                            // rutinas....
                                            echo "$ValorNumerico";
                                        }

                                        echo '</td>';


                                    }

                                }
                                // cerramos X
                                echo '</tr>';






                                echo '<tr>';
                                $masCuatro = 2;
                                $masCinco = 4;
                                for ($s = 0; $s < $columnas; ++$s) {
                                    if ($s == 0) {

                                        echo '<td>';
                                        // rutinas....
                                        echo "$Calculos_array[$s]";
                                        echo '</td>';


                                    } else {

                                        $ValorNumerico = number_format($Calculos_array[$s], 2);

                                        if ($s == $masCuatro) {
                                            echo '<td style="width: 15px; border-left-style: double;">';
                                            $masCuatro = $masCuatro + 4;
                                        } else {

                                            echo '<td>';

                                        }


                                        if ($s == $masCinco) {
                                            if ($Calculos_array[$s - 2] <= 0) {
                                                $ValorNumerico = number_format(0, 2);
                                            } else {
                                                $calculo = ($Calculos_array[$s - 1] / $Calculos_array[$s - 2]) * 100;
                                                $ValorNumerico = number_format($calculo, 2);
                                            }
                                            $masCinco = $masCinco + 4;

                                            if ($ValorNumerico >= 0 and $ValorNumerico <= 50) {

                                                $validador = "bg-danger color-palette";

                                            } else {

                                                if ($ValorNumerico >= 51 and $ValorNumerico <= 80) {

                                                    $validador = "bg-warning color-palette";

                                                } else {

                                                    if ($ValorNumerico >= 81) {
                                                        $validador = "bg-success color-palette";
                                                    }
                                                }

                                            }

                                            $validadorValor = '<div class=' . $validador . ' ><span> ' . $ValorNumerico . ' </span></div>';
                                            // rutinas....
                                            echo "$validadorValor";

                                        } else {

                                            // rutinas....
                                            echo "$ValorNumerico";
                                        }

                                        echo '</td>';


                                    }

                                }
                                // cerramos X
                                echo '</tr>';

                                echo '</tbody>';
                                echo '</table>';
                                ?>



                                <div class="row text-center">
                                    <div class="col-sm-1">
                                        <div class="bg-danger color-palette"><span>ROJO: 0 - 50% </span></div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="bg-warning color-palette"><span>AMARILLO: 51 - 80%</span></div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="bg-success color-palette"><span>VERDE: 81 - 100% </span></div>
                                    </div>
                                </div>

                                <hr>
                                <!--</div>-->
                                <!-- /.container-fluid -->
                                <div class="container">
                                    <a href="reporteKpiMarcas_excel.php?&fecha=<?php echo $_GET['fecha']; ?>"
                                        class="card-link" id="btn_excel">
                                        <?= Strings::titleFromJson('boton_excel') ?>
                                    </a>

                                    <!-- <a href="kpi_pdf.php?&fechai=<?php /*echo $_GET['fechai']; */?>&fechaf=<?php /*echo $_GET['fechaf']; */?>&d_habiles=<?php /*echo $_GET['d_habiles']; */?>&d_trans=<?php /*echo $_GET['d_trans']; */?>" class="card-link" id="btn_pdf" target="_blank">
                    <?= Strings::titleFromJson('boton_pdf') ?>
                </a>-->
                                </div>
            </div>

            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->



        <input id="id" type="hidden" value="<?php echo $_SESSION['cedula']; ?>" />
        <!-- Main Footer -->
        <?php require_once("../footer.php"); ?>
        <script src="<?php echo URL_HELPERS_JS; ?>Number.js" type="text/javascript"></script>

        <script type="text/javascript" src="reporteKpiMarcas_tabla.js"></script>
    </div>
    <!-- ./wrapper -->

</body>

</html>


<script>
    const arr = ['#tabla', '#tabla1'];
    arr.forEach(val => {
        $(val).columntoggle({
            //Class of column toggle contains toggle link
            toggleContainerClass: 'columntoggle-container',
            //Text in column toggle box
            toggleLabel: 'MOSTRAR/OCULTAR CELDAS: ',
            //the prefix of key in localstorage
            keyPrefix: 'columntoggle-',
            //keyname in localstorage, if empty, it will get from URL
            key: ''

        });
    });
</script>