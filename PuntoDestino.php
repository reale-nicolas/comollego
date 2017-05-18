<?php

require_once "Nodo.php";
require_once "NodoTipo.php";

class PuntoDestino extends Nodo {

    public function __construct($latitud, $longitud) 
    {
        parent::__construct($latitud, $longitud, NodoTipo::$DESTINO);
    }
}
