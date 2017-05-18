<?php


class Enlace {
    private $id;
    private $puntoGeografico1;
    private $puntoGeografico2;
    private $peso;
    private $distancia;    
    private $duracion;
    private $medioTranporte;
    
    public function __construct($id, $puntoGeografico1, $puntoGeografico2, $peso) 
    {
        $this->setId($id);
        $this->setPuntoGeografico1($puntoGeografico1);
        $this->setPuntoGeografico2($puntoGeografico2);
        $this->setPeso($peso);
    }
    
    
    /**
     * @param  $punto1
     */
    public function setPuntoGeografico1($puntoGeografico1) {
        $this->puntoGeografico1 = $puntoGeografico1;
    }
    /**
     * @return 
     */
    public function getPuntoGeografico1() {
        return $this->puntoGeografico1;
    }
    /**
     * @param  $punto2
     */
    public function setPuntoGeografico2($puntoGeografico2) {
        $this->puntoGeografico2 = $puntoGeografico2;
    }
    /**
     * @return 
     */
    public function getPuntoGeografico2() {
        return $this->puntoGeografico2;
    }
    /**
     * @param  $peso
     */
    public function setPeso($peso) {
        $this->peso = $peso;
    }
    /**
     * @return 
     */
    public function getPeso() {
        return $this->peso;
    }
    /**
     * @param  $distancia
     */
    public function setDistancia($distancia) {
        $this->distancia = $distancia;
    }
    /**
     * @return 
     */
    public function getDistancia() {
        return $this->distancia;
    }
    /**
     * @param  $duracion
     */
    public function setDuracion($duracion) {
        $this->duracion = $duracion;
    }
    /**
     * @return 
     */
    public function getDuracion() {
        return $this->duracion;
    }
    /**
     * @param  $medioTranporte
     */
    public function setMedioTranporte($medioTranporte) {
        $this->medioTranporte = $medioTranporte;
    }
    /**
     * @return 
     */
    public function getMedioTranporte() {
        return $this->medioTranporte;
    }
    /**
     * @param  $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    /**
     * @return 
     */
    public function getId() {
        return $this->id;
    }
    
    public function asXml()
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $enlace = $xml->createElement("enlace");
        $enlace = $xml->appendChild($enlace);
        
        $peso = $xml->createElement("peso", $this->getPeso());
        $peso = $enlace->appendChild($peso);
        
        $distancia = $xml->createElement("distancia", $this->getDistancia());
        $distancia = $enlace->appendChild($distancia);
        
        $duracion = $xml->createElement("duracion", (is_null($this->getDuracion()) ? 0 : $this->getDuracion()));
        $duracion = $enlace->appendChild($duracion);
        
        $medioTransporte = $xml->createElement("mediotransporte", $this->getMedioTranporte());
        $medioTransporte = $enlace->appendChild($medioTransporte);
        
        
        $xmlPG1 = $this->getPuntoGeografico1()->asXml();
        $xmlPG2 = $this->getPuntoGeografico2()->asXml();
        
        $nodo1 = $xmlPG1->getElementsByTagName("nodo");
        $nodo2 = $xmlPG2->getElementsByTagName("nodo");
        
        foreach($nodo1 as $nodo) {
            $enlace->appendChild($xml->importNode($nodo, true));
        }
        foreach($nodo2 as $nodo) {
            $enlace->appendChild($xml->importNode($nodo, true));
        }
      
       return $xml;
    }
}
