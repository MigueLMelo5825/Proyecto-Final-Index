<?php

class FormatearEventos {

    public static function generarDescripcion($evento, $idActual) {

        $esPropio = ($evento['id_usuario'] == $idActual);
        $usuario = '@' . $evento['username'];

        switch ($evento['tipo']) {

            case 'pelicula':
                preg_match("/'([^']+)'/", $evento['descripcion'], $m);
                $titulo = $m[1] ?? 'una película';

                return $esPropio
                    ? "Has añadido '$titulo' a tu lista"
                    : "$usuario ha añadido '$titulo' a su lista";

            case 'lista_creada':
                preg_match("/'([^']+)'/", $evento['descripcion'], $m);
                $nombreLista = $m[1] ?? 'una lista';

                return $esPropio
                    ? "Has creado la lista '$nombreLista'"
                    : "$usuario ha creado la lista '$nombreLista'";

            case 'registro':
                return $esPropio
                    ? "Te has registrado en la plataforma"
                    : "$usuario se ha registrado en la plataforma";

            default:
                return $esPropio
                    ? $evento['descripcion']
                    : "$usuario ha realizado una acción";
        }
    }
}
