<?php


class PermisosHelpers
{
    public static function verficarAcceso($ruta) {
        $permiso = Permisos::verficarPermisoPorSessionUsuario($ruta);

        # si retorna al menos un registro, tiene permisos
        return count($permiso) > 0;
    }

    public static function registrarPermisoPorRol($data) {
        $permiso = false;

        $usuarios = Usuarios::byRol($data['id']);
        if (is_array($usuarios)==true and count($usuarios)>0) {
            foreach ($usuarios as $usuario) {
                $data1 = array(
                    'id' => $usuario['cedula'],
                    'modulo_id' => $data['modulo_id'],
                );
                $permiso = Permisos::registrar_permiso($data1);
                if (!$permiso) break;
            }
        }

        return $permiso;
    }

    public static function borrarPermisoPorRol($data) {
        $permiso = false;

        $usuarios = Usuarios::byRol($data['id']);
        if (is_array($usuarios)==true and count($usuarios)>0) {
            foreach ($usuarios as $usuario) {
                $data1 = array(
                    'id' => $usuario['cedula'],
                    'modulo_id' => $data['modulo_id'],
                );
                $permiso = Permisos::borrar_permiso($data1);
                if (!$permiso) break;
            }
        }

        return $permiso;
    }

}