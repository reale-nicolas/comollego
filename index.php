<?php
//ini_set("display_errors", false);
set_time_limit(120);
   
date_default_timezone_set('UTC');
$start = mktime();    
//echo "Iniciando: ".$start."</br></br>";
/*echo "Hola <b> pochita mia! :) </b> Adivina quien aprendiò a poner mensajes en tu programa! ";
echo "<br>";
echo "<br>";
echo "Adivina quien googleo para acordarse como poner un salto de linea :)";
echo "<br>";
echo "Adivina quien googleo para acordarse como poner <b> negrita </b> :)";
echo "<br>";
echo "T'estimo <3";
echo "<br>";*/


require_once "PuntoOrigen.php";
require_once "PuntoDestino.php";
require_once "Grafo.php";
require_once "RecorridoControlador.php";
require_once "funciones.php";
require_once "constantes.php";
require_once "EnlaceControlador.php";
require_once "RutaOptima.php";

define('I',1500000);   

$origenLat = -24.735772;
$origenlong = -65.411512;

/*$destinoLong= -65.415677;
$destinoLat=-24.779373;*/
$distanciaMaximaAPie = 500;
//$origenLat = -24.814075;
//$origenlong = -65.384607;

//$destinoLat=-24.789255;
//$destinoLong =-65.404770;

$destinoLat=-24.842006;
$destinoLong =-65.461151;


//$destinoLat=-24.790569;
//$destinoLong =-65.412281;

$nodoOrigen = new PuntoOrigen($origenLat, $origenlong);
$nodoDestino = new PuntoDestino($destinoLat, $destinoLong);

$recorridosControlador = new RecorridoControlador();
$recorridosControlador->setUpRecorridos();

$recordidosCercanosAlOrigen = $recorridosControlador->getRecorridoByRadio($nodoOrigen, $distanciaMaximaAPie);
$recordidosCercanosAlDestino = $recorridosControlador->getRecorridoByRadio($nodoDestino, $distanciaMaximaAPie);
//echo "Corredores cercanos al origen<pre>";print_r($recordidosCercanosAlOrigen);echo "</pre>";
//echo "Corredores cercanos al destino<pre>";print_r($recordidosCercanosAlDestino);echo "</pre>";
$grafo = new Grafo($nodoOrigen, $recordidosCercanosAlOrigen, $nodoDestino, $recordidosCercanosAlDestino);
$rutaOptima = new RutaOptima($nodoOrigen, $nodoDestino, null, $recorridosControlador, null);

do {
    $continuarScript = false;
    
//    echo "Corredores cercanos al origen<pre>";print_r($recordidosCercanosAlOrigen);echo "</pre>";
//    echo "Corredores cercanos al destino<pre>";print_r($recordidosCercanosAlDestino);echo "</pre>";
    $grafo->setEnlacesControlador(new EnlaceControlador());
    $grafo->setCorredoresCercanosAlOrigen($recordidosCercanosAlOrigen);
    $grafo->setCorredoresCercanosAlDestino($recordidosCercanosAlDestino);
    
    $grafo->setUpGrafo();
    $mejorRuta = $grafo->getCaminosMasCortos();
//echo "mejorRuta<pre>";print_r($mejorRuta);echo "</pre>";
    $enlacesControlador = $grafo->getEnlacesControlador();
    
    $rutaOptima->setRutaOptima($mejorRuta);
    $rutaOptima->setEnlaceControlador($enlacesControlador);
    $rutaOptima->setUp();
    
    $corredor1 = $rutaOptima->arrCorredoresUtilizados[0];
//  echo "<pre>";print_r($corredor1);echo "</pre>";
  
    if ( in_array($corredor1, $recordidosCercanosAlOrigen) && count ($recordidosCercanosAlOrigen) > 1)
    {
        $continuarScript = true;
        unset($recordidosCercanosAlOrigen[array_search($corredor1, $recordidosCercanosAlOrigen)]);
        $recordidosCercanosAlOrigen = array_values(array_filter($recordidosCercanosAlOrigen));
//        foreach ($recordidosCercanosAlOrigen as $k => $corredorCercano)
//            if($corredor1 == $corredorCercano) {
//                unset($recordidosCercanosAlOrigen[$k]);
//                $recordidosCercanosAlOrigenAux = array_values(array_filter($recordidosCercanosAlOrigen));
//            }
    }   
    else if ( in_array($corredor1, $recordidosCercanosAlDestino) && count ($recordidosCercanosAlDestino) > 1 ) 
    {
        $continuarScript = true;
        foreach ($recordidosCercanosAlDestino as $k => $corredorCercano)
            if($corredor1 == $corredorCercano) {
                unset($recordidosCercanosAlDestino[$k]);
                ksort($recordidosCercanosAlDestino);
            }
    }   
//    echo "<pre>";print_r($corredor1);echo "</pre>";
} while ($continuarScript == true);
$rutaOptima->asXml();