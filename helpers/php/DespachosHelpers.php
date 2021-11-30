<?php

class DespachosHelpers
{
    public static function validateExistDocumentInString(string $records_to_be_dispatched, string $numerod, string $tipofac) : int {
        $aux = 0;
        # consultamos si el existe en el despacho por crear
        if(Strings::avoidNullOrEmpty($records_to_be_dispatched)) {

            # separa por cada ";" quedando el formato dispuesto:
            #   numerod-tipofac-peso
            $records = explode(";", substr($records_to_be_dispatched, 0, -1));

            foreach ($records as $record) {
                # separa por cada "-" quedando el formato anterior mencionado
                # [0] -> numerod
                # [1] -> tipofac
                # [2] -> peso (tara)
                $data = explode("-", $record);

                if ($data[0] == $numerod  and  $data[1] == $tipofac) {
                    $aux++;
                }
            }
        }
        return $aux;
    }

    public static function validateWeightAndCubicCapacity($data) {
        $output = array(
            'peso_max'     => $data['peso_max'],
            'cubicaje_max' => $data['cubicaje_max'],
        );
        $porcentajePeso = 1;
        $porcentajeCubicaje = 1;

        # consulta si el peso nuevo + el peso acumulado es < que el peso total del camion
        if( (floatval($data['peso']) + floatval($data['peso_acum']) ) < floatval($data['peso_max']) ){

            # calcula el porcentaje del peso a agregar
            $porcentajePeso = ((floatval($data['peso']) + floatval($data['peso_acum'])) * 100) / floatval($data['peso_max']);

            # calcula el porcentaje de cubicaje a agregar
            $porcentajeCubicaje = ((floatval($data['cubicaje']) + floatval($data['cubicaje_acum'])) * 100) / floatval($data['cubicaje_max']);

            # asigna el peso y cubicaje nuevo + el acumulado
            $output["pesoNuevoAcum"] = floatval($data['peso']) + floatval($data['peso_acum']);
            $output["cubicajeNuevoAcum"] = floatval($data['cubicaje_acum']) + floatval($data['cubicaje']);
            $output["cond"] = true;
        }
        # sino, solo devuelve el acumulado anterior y avisa que el acumulado supera al maximo de carga con la cond
        else {

            # calcula el porcentaje del peso anterior
            $porcentajePeso = (floatval($data['peso_acum']) * 100) / floatval($data['peso_max']);

            # calcula el porcentaje de cubicaje anterior
            $porcentajeCubicaje = (floatval($data['cubicaje_acum']) * 100) / floatval($data['cubicaje_max']);

            # asigna el peso y cubicaje anterior
            $output["pesoNuevoAcum"] = floatval($data['peso_acum']);
            $output["cubicajeNuevoAcum"] = floatval($data['cubicaje_acum']);
            $output["cond"] = false;
        }

        # evaluacion del color de la barra de progreso del peso acumulado
        $bgProgress = "";
        if(floatval($porcentajePeso) >= 0 && floatval($porcentajePeso) <70){
            $bgProgress = "bg-success";
        } elseif(floatval($porcentajePeso) >= 70 && floatval($porcentajePeso) <90){
            $bgProgress = "bg-warning";
        }elseif (floatval($porcentajePeso) >= 90 && floatval($porcentajePeso) <=100){
            $bgProgress = "bg-danger";
        }
        $output["porcentajePeso"] = $porcentajePeso;
        $output["porcentajeCubicaje"] = $porcentajeCubicaje;
        $output["bgProgreso"] = $bgProgress;

        return $output;
    }


}