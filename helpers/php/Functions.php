<?php


class Functions {

    public static function listDirectory($directorio)
    {
        $output = array();

        $listado = scandir($directorio);
        unset($listado[array_search('.', $listado, true)]);
        unset($listado[array_search('..', $listado, true)]);
        if (count($listado) < 1) {
            return [];
        }
        foreach($listado as $elemento)
        {
            # PARA LISTAR ARCHIVOS
            /* if(!is_dir($directorio.'/'.$elemento)) {
                $output[] = "$elemento";
            }*/

            # PARA LISTAR CARPETAS
            if(is_dir($directorio.'/'.$elemento)) {
                $output[] = "$elemento";
                Functions::listDirectory($directorio.'/'.$elemento);
            }
        }

        return $output;
    }

    public static function searchQuantityDocumentsByDates($array, $fieldSearch, $search, $format)
    {
        $indexI = $indexF = 0;
        $flag = true;

        for ($i=0; $i<count($array)-1&&$flag==true; $i++) {
        $indexI=$i;
        if (date_format(date_create($search), $format) == date_format(date_create($array[$i][$fieldSearch]), $format)) {
        for($j=$i+1; $j<count($array) && date_format(date_create($search), $format)==date_format(date_create($array[$j-1][$fieldSearch]), $format); $j++) {
        $indexF=$j;
        }
        $flag=false;
        }
        }
        return $indexF - $indexI;
    }

    public static function selectListMenus($id, $selectChangeToNone = false, $id_menu_except = -1)
    {
        $output = '';
        $datos = Menu::todos();
        $seleccionado = Menu::getById($id);
        $haySeleccionado = (is_array($seleccionado) == true and count($seleccionado) > 0);


        $output .= '<option value="-1">' . ( $selectChangeToNone ? 'Ninguno' : '--Seleccione--' ). '</option>';
        if (is_array($datos) == true and count($datos) > 0)
        {
            foreach ($datos as $key => $row)
            {
                if($id_menu_except != $row['id'])
                {
                    if ($haySeleccionado and ($row['id']==$seleccionado[0]['id']) )
                        $output .= '<option value="' . $row['id'] . '" selected>' . $row['nombre'] . '</option>';
                    else
                        $output .= '<option value="' . $row['id'] .'">'. $row['nombre'] .'</option>';
                }
            }
        }

        return $output;
    }

    public static function listModulesAvailable($id_modulo)
    {
        $output = array();
        $directorios_en_db  = array_map(function ($arr) { return $arr['ruta']; }, Modulos::todos());
        $directorios_en_app = Functions::listDirectory(PATH_APP_PHP);
        $directorio_seleccionado = Modulos::getById($id_modulo);

        if (is_array($directorio_seleccionado) == true and count($directorio_seleccionado) > 0)
            $output[] = $directorio_seleccionado[0]['ruta'];

        foreach ($directorios_en_app as $directorio)
            if (!in_array($directorio, $directorios_en_db))
                $output[] = $directorio;

        return $output;
    }

    public static function orgranigramaMenus($id = -1) {
        $output = array();

        $hijos = ($id==-1) ? Menu::withoutFather() : Menu::getChildren($id);
        if (is_array($hijos) == true and count($hijos) > 0)
        {
            foreach ($hijos as $key => $hijo)
            {
                $sub_array = array();

                $sub_array['title'] = $hijo['nombre'];
                $sub_array['name'] = $hijo['nombre'];

                $existenHijos = Menu::getChildren($hijo['id']);
                if (is_array($existenHijos) == true and count($existenHijos) > 0) {
                    $sub_array['children'] = Functions::orgranigramaMenus($hijo['id']);
                }

                $output[] = $sub_array;
            }
        }
        return $output;
    }

    public static function organigramaMenusWithModules($id, $rol_id = -1){
        $output = array();

        $hijos = ($id==-1) ? Menu::withoutFather() : Menu::getChildren($id);
        if (is_array($hijos) == true and count($hijos) > 0)
        {
            foreach ($hijos as $key => $hijo) {
                $sub_array = array();

                $sub_array['title'] = $hijo['nombre'];

                # verifica si existe hijos para aplicar recursion
                $existenHijos = Menu::getChildren($hijo['id']);
                $sub_array['children'] = (is_array($existenHijos) == true and count($existenHijos) > 0)
                    ? Functions::organigramaMenusWithModules($hijo['id'], $rol_id)
                    : array();


                 # verificamos si tiene modulos
                $modulosMenu = array();
                $existenModulos = Modulos::getByMenuId($hijo['id']);
                if (is_array($existenModulos) == true and count($existenModulos) > 0) {
                    $modulosPorRol = array_map(function ($arr) { return $arr['id_modulo']; }, Permisos::getRolesGrupoPorRolID($rol_id));
                    foreach ($existenModulos as $key1 => $modulo) {
                        $modulosMenu[] = array(
                            'id' 	   => $modulo['id'],
                            'name'   => $modulo['nombre'],
                            'selected' => in_array($modulo['id'], $modulosPorRol)
                        );
                    }
                }

                $sub_array['modules'] = $modulosMenu;

                $output[] = $sub_array;
            }
        }
        return $output;
    }

}