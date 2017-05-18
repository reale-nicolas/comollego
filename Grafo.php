<?php

require_once "Dijkstra.php";
require_once "Enlace.php";
require_once "EnlaceControlador.php";
require_once "PuntoGeografico.php";
require_once "MedioTransporte.php";
require_once "funciones.php";

class Grafo {
    private $origen;
    private $destino;
    private $corredoresCercanosAlOrigen;
    private $corredoresCercanosAlDestino; 
    
//    private $enlaces = array();
//    private $nodos = array();

    private $enlacesControlador;
    private $nodosControlador;
    
    public function __construct($origen, $corredoresCercanosAlOrigen, $destino, $corredoresCercanosAlDestino) 
    {
        $this->setEnlacesControlador(new EnlaceControlador());
        $this->setOrigen($origen);
        $this->setCorredoresCercanosAlOrigen($corredoresCercanosAlOrigen);
        $this->setDestino($destino);
        $this->setCorredoresCercanosAlDestino($corredoresCercanosAlDestino);
    }
    
    
    public function setUpGrafo()
    {
        $id = 0;
        
        /**
         * Creamos el enlace entre el punto geografico de origen y el punto de destino
         **/
        /*$distancia = distancia(
                                    $this->getOrigen()->getLatitud(), 
                                    $this->getOrigen()->getLongitud(), 
                                    $this->getDestino()->getLatitud(), 
                                    $this->getDestino()->getLongitud(), 
                                    "m", 
                                    2
        );
        $peso = $distancia * CTE_PESO_CAMINO_A_PIE;
        
        $enlace = new Enlace($id, $this->getOrigen(), $this->getDestino(), $peso);
        $enlace->setDistancia($distancia);
        $enlace->setMedioTranporte(MedioTransporte::$BY_FOOT);
        
        $this->addEnlace($enlace);
        $id++;*/
        /**
         * Creamos todos los enlaces entre el punto geografico de origen
         * y las paradas pertenecientes a los corredores que pasan cerca del origen
         **/
        foreach($this->getCorredoresCercanosAlOrigen() as $corredorOrigen) 
        {
            foreach($corredorOrigen->getParadas() as $parada)
            {
                $distancia = distancia(
                                    $this->getOrigen()->getLatitud(), 
                                    $this->getOrigen()->getLongitud(), 
                                    $parada->getLatitud(), 
                                    $parada->getLongitud(), 
                                    "m", 
                                    2
                );
                $peso = $distancia * CTE_PESO_CAMINO_A_PIE;
                
                $enlace = new Enlace($id, $this->getOrigen(), $parada, $peso);
                $enlace->setDistancia($distancia);
                $enlace->setMedioTranporte(MedioTransporte::$BY_FOOT);
                
                $this->getEnlacesControlador()->addEnlace($enlace);
                $id++;
            }
        }
        /**
         * Creamos todos los enlaces entre el punto geografico de destino
         * y las paradas pertenecientes a los corredores que pasan cerca del origen
         **/
        /*foreach($this->getCorredoresCercanosAlOrigen() as $corredorOrigen) 
        {
            foreach($corredorOrigen->getParadas() as $parada)
            {
                $distancia = distancia(
                                    $this->getDestino()->getLatitud(), 
                                    $this->getDestino()->getLongitud(), 
                                    $parada->getLatitud(), 
                                    $parada->getLongitud(), 
                                    "m", 
                                    2
                );
                $peso = $distancia * CTE_PESO_CAMINO_A_PIE;
                
                $enlace = new Enlace($this->getDestino(), $parada, $peso);
                $enlace->setDistancia($distancia);
                $enlace->setMedioTranporte(MedioTransporte::$BY_FOOT);
                
                $this->addEnlace($enlace);
            }
        }*/
        
        /**
         * Creamos todos los enlaces entre el punto geografico que pertencen
         * a los corredores que pasan cerca del origen, esto es entre las mismas
         * paradas de un corredor
         **/
        foreach($this->getCorredoresCercanosAlOrigen() as $corredorOrigen) 
        {
            $bPrimero = true;
            foreach($corredorOrigen->getParadas() as $parada)
            {
                if (!$bPrimero) {
                    $distancia = distancia(
                                $parada->getLatitud(), 
                                $parada->getLongitud(), 
                                $ultimaParada->getLatitud(), 
                                $ultimaParada->getLongitud(),
                                "m", 
                                2
                    );
                    $peso = $distancia;
                    
                    $enlace = new Enlace($id, $ultimaParada, $parada, $peso);
                    $enlace->setDistancia($distancia);
                    $enlace->setMedioTranporte(MedioTransporte::$BY_BUS);
                    
                    $this->getEnlacesControlador()->addEnlace($enlace);  
                    $id++;           
                }
                $ultimaParada = $parada;
                $bPrimero = false;
            }
        }
        
        /**
         * Creamos todos los enlaces entre el punto geografico que pertencen
         * a los corredores que pasan cerca del destino, esto es entre las mismas
         * paradas de un corredor
         **/
        foreach($this->getCorredoresCercanosAlDestino() as $corredorDestino) 
        {
            $bPrimero = true;
            foreach($corredorDestino->getParadas() as $parada)
            {
                if (!$bPrimero) {
                    $distancia = distancia(
                                $ultimaParada->getLatitud(), 
                                $ultimaParada->getLongitud(),
                                $parada->getLatitud(), 
                                $parada->getLongitud(), 
                                "m", 
                                2
                    );
                    $peso = $distancia;
                    
                    $enlace = new Enlace($id, $ultimaParada, $parada, $peso);
                    $enlace->setDistancia($distancia);
                    $enlace->setMedioTranporte(MedioTransporte::$BY_BUS);
                    
                    $this->getEnlacesControlador()->addEnlace($enlace); 
                    $id++;           
                }
                $ultimaParada = $parada;
                $bPrimero = false;
            }
        }
        
        /**
         * Creamos todos los enlaces entre el todas las paradas
         * pertenecientes a los distintos corredores
         **/
        foreach($this->getCorredoresCercanosAlOrigen() as $corredorOrigen) 
        {
            foreach($corredorOrigen->getParadas() as $paradaOrigen)
            {
        
                foreach($this->getCorredoresCercanosAlDestino() as $corredorDestino) 
                {
                    foreach($corredorDestino->getParadas() as $paradaDestino)
                    {
                        if ($corredorDestino != $corredorOrigen)
                        {
                            $distancia = distancia(
                                        $paradaOrigen->getLatitud(), 
                                        $paradaOrigen->getLongitud(), 
                                        $paradaDestino->getLatitud(), 
                                        $paradaDestino->getLongitud(), 
                                        "m", 
                                        2
                            );
                            $peso = $distancia * CTE_PESO_CAMINO_A_PIE * CTE_PESO_CAMBIO_COLECTIVO;
                            
                            $enlace = new Enlace($id, $paradaOrigen, $paradaDestino, $peso);
                            $enlace->setDistancia($distancia);
                            $enlace->setMedioTranporte(MedioTransporte::$BY_FOOT);
                            
                            $this->getEnlacesControlador()->addEnlace($enlace);
                            $id++;
                        }
                    }
                }
            }
        }
        
        
        foreach($this->getCorredoresCercanosAlDestino() as $corredorDestino) 
        {
            foreach($corredorDestino->getParadas() as $parada)
            {
                $distancia = distancia(
                                    $this->getDestino()->getLatitud(), 
                                    $this->getDestino()->getLongitud(), 
                                    $parada->getLatitud(), 
                                    $parada->getLongitud(), 
                                    "m", 
                                    2
                );
                $peso = $distancia * CTE_PESO_CAMINO_A_PIE;
                
                $enlace = new Enlace($id, $parada, $this->getDestino(), $peso);
                $enlace->setDistancia($distancia);
                $enlace->setMedioTranporte(MedioTransporte::$BY_FOOT);
                
                $this->getEnlacesControlador()->addEnlace($enlace);
                $id++;
            }
        }
//        echo "La cantidad de conexiones es: ".count($this->getEnlacesControlador()->getEnlaces());
    }
    
    
    public function getEnlacesDikjstraFormat()
    {
        $arrPoints = array();
        $ourMap = array();
        foreach ($this->getEnlacesControlador()->getEnlaces() as $enlace)
        {
            $arrPoints[] = array(
                        (double)$enlace->getPuntoGeografico1()->getIdDikjstra(), 
                        (double)$enlace->getPuntoGeografico2()->getIdDikjstra(), 
                        $enlace->getPeso()
                    );
        }
        
        for ($i = 0,$m = count($arrPoints); $i < $m; $i = $i + 1) { 
            $x = $arrPoints[$i][0]; 
            $y = $arrPoints[$i][1]; 
            $c = $arrPoints[$i][2]; 
            $ourMap[$x][$y] = $c; 
        }
        
        $matrixWidth = count($ourMap);
        // ensure that the distance from a node to itself is always zero 
        // Purists may want to edit this bit out. 
        for ($i = 0; $i < $matrixWidth; $i = $i+1) {     
            for ($k = 0; $k < $matrixWidth; $k = $k+1) {         
                if ($i == $k) 
                    $ourMap[$i][$k] = 0;     
            } 
        }
        
        return $ourMap;
        
    }
    
    
    public function getCaminosMasCortos() 
    {
        $mapDikjstra = $this->getEnlacesDikjstraFormat();

        $dijkstra = new Dijkstra($mapDikjstra, I);   
        
        // $dijkstra->findShortestPath(0,13); to find only path from field 0 to field 13...
        $dijkstra->findShortestPath(0);   
        // Display the results   

         return $dijkstra -> getResults($this->getDestino()->getIdDikjstra()); 
//         echo "<pre>";print_r($mejorRuta);echo "</pre>";
    }
    
    
      
    
    /**
     * @param  $origen
     */
    public function setOrigen($origen) {
        $this->origen = $origen;
    }
    /**
     * @return 
     */
    public function getOrigen() {
        return $this->origen;
    }
    /**
     * @param  $destino
     */
    public function setDestino($destino) {
        $this->destino = $destino;
    }
    /**
     * @return 
     */
    public function getDestino() {
        return $this->destino;
    }
    /**
     * @param  $corredoresCercanosAlOrigen
     */
    public function setCorredoresCercanosAlOrigen($corredoresCercanosAlOrigen) {
        $this->corredoresCercanosAlOrigen = $corredoresCercanosAlOrigen;
    }
    /**
     * @return 
     */
    public function getCorredoresCercanosAlOrigen() {
        return $this->corredoresCercanosAlOrigen;
    }
    /**
     * @param  $corredoresCercanosAlDestino
     */
    public function setCorredoresCercanosAlDestino($corredoresCercanosAlDestino) {
        $this->corredoresCercanosAlDestino = $corredoresCercanosAlDestino;
    }
    /**
     * @return 
     */
    public function getCorredoresCercanosAlDestino() {
        return $this->corredoresCercanosAlDestino;
    }
    /**
     * @param  $enlacesControlador
     */
    public function setEnlacesControlador($enlacesControlador) {
        $this->enlacesControlador = $enlacesControlador;
    }
    /**
     * @return 
     */
    public function getEnlacesControlador() {
        return $this->enlacesControlador;
    }
}
