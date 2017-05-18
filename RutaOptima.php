<?php


class RutaOptima 
{
    private $origen;
    private $destino;
    private $rutaOptima;
    private $recorridosControlador;
    private $enlaceControlador;
    
    private $document;
    public $arrCorredoresUtilizados = array();
    
    public function __construct($origen, $destino, $rutaOptima, $recorridosControlador, $enlaceControlador) 
    {
        $this->setOrigen($origen);
        $this->setDestino($destino);
        $this->setRutaOptima($rutaOptima);
        $this->setRecorridosControlador($recorridosControlador);
        $this->setEnlaceControlador($enlaceControlador);
        $this->document = new Document();
    }
    
    public function asXml()
    {
        header("Content-type: application/xml");
        $xml = $this->document->asXml();
        $xml->formatOutput = true;
        $elXML = $xml->saveXML();
        $xml->save("recorridosOpTIMOS11.xml");
        echo $elXML;
    }
    
    public function setUp()
    {
        $this->arrCorredoresUtilizados = array();
        $arrTramos = array();
        $arrEnlaces = array();
        
        $nodosASeguir = $this->getRutaOptima();
        $nodosASeguir = $nodosASeguir[$this->getOrigen()->getIdDikjstra()][$this->getDestino()->getIdDikjstra()]["nodos"];

        foreach ($nodosASeguir as $k => $conexion)
        {
            $enlace = $this->getEnlaceControlador()->getEnlace($conexion, $nodosASeguir[$k+1]);
            if (!is_null($enlace))
                $arrEnlaces[] = $enlace;
        }
//        echo "<pre>";print_r($arrEnlaces);echo "</pre>";
        $ultimoMedioDeTransporte = null;
        foreach ($arrEnlaces as $enlace)
        {
            if ($enlace->getMedioTranporte() === $ultimoMedioDeTransporte) 
            {
                $enlaceTramo1 = new EnlaceTramo(
                                                $enlace->getPuntoGeografico1()->getIdDikjstra(), 
                                                $enlace->getPuntoGeografico1()->getLatitud(), 
                                                $enlace->getPuntoGeografico1()->getLongitud());
                                                
                $enlaceTramo2 = new EnlaceTramo($enlace->getPuntoGeografico2()->getIdDikjstra(),
                                                $enlace->getPuntoGeografico2()->getLatitud(), 
                                                $enlace->getPuntoGeografico2()->getLongitud());
                $tramo->addEnlaceTramo($enlaceTramo1);
                $tramo->addEnlaceTramo($enlaceTramo2);
                
                $tramo->distancia = $tramo->distancia + $enlace->getDistancia();
            } 
            else 
            {
                $tramo = new Tramo();
                $enlaceTramo1 = new EnlaceTramo(
                                                $enlace->getPuntoGeografico1()->getIdDikjstra(), 
                                                $enlace->getPuntoGeografico1()->getLatitud(), 
                                                $enlace->getPuntoGeografico1()->getLongitud());
                                                
                $enlaceTramo2 = new EnlaceTramo($enlace->getPuntoGeografico2()->getIdDikjstra(),
                                                $enlace->getPuntoGeografico2()->getLatitud(), 
                                                $enlace->getPuntoGeografico2()->getLongitud());
                
                $tramo->distancia = $enlace->getDistancia();
                $tramo->addEnlaceTramo($enlaceTramo1);
                $tramo->addEnlaceTramo($enlaceTramo2);
                $tramo->medioTransporte = $enlace->getMedioTranporte();
                
//                echo "<pre>";print_r($enlace);echo "</pre>";
                if($enlace->getMedioTranporte() == MedioTransporte::$BY_BUS)
                {
                    $tramo->corredor = $enlace->getPuntoGeografico1()->getRecorridoColectivo()->getCorredor();
                    $tramo->ramal = $enlace->getPuntoGeografico1()->getRecorridoColectivo()->getRamal();
                    $tramo->zona = $enlace->getPuntoGeografico1()->getRecorridoColectivo()->getZona();
                    $this->arrCorredoresUtilizados[] = $enlace->getPuntoGeografico1()->getRecorridoColectivo();
                }
                $ultimoMedioDeTransporte = $enlace->getMedioTranporte();
                
                $arrTramos[] = $tramo;
            }
        }
        
        $alternativa = new Alternativa();
        $alternativa->arrTramos = $arrTramos;   
        $this->document->addAlternativa($alternativa);
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
     * @param  $rutaOptima
     */
    public function setRutaOptima($rutaOptima) {
        $this->rutaOptima = $rutaOptima;
    }
    /**
     * @return 
     */
    public function getRutaOptima() {
        return $this->rutaOptima;
    }
    /**
     * @param  $recorridosControlador
     */
    public function setRecorridosControlador($recorridosControlador) {
        $this->recorridosControlador = $recorridosControlador;
    }
    /**
     * @return 
     */
    public function getRecorridosControlador() {
        return $this->recorridosControlador;
    }
    /**
     * @param  $enlaceControlador
     */
    public function setEnlaceControlador($enlaceControlador) {
        $this->enlaceControlador = $enlaceControlador;
    }
    /**
     * @return 
     */
    public function getEnlaceControlador() {
        return $this->enlaceControlador;
    }
}

class Document
{
    public $arrAlternativa;
    
    public function addAlternativa($alternativa)
    {
        $this->arrAlternativa[] = $alternativa;
    }
    
    
    public function asXml()
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $document = $xml->createElement("document");
        $document = $xml->appendChild($document);
        
        foreach ($this->arrAlternativa as $alternativa)
        {
            $xmlAlternativa = $alternativa->asXml();
            
            $xmlArrAlternativa = $xmlAlternativa->getElementsByTagName("alternativa");
            foreach($xmlArrAlternativa as $alternativa) {
                $document->appendChild($xml->importNode($alternativa, true));
            }
            
        }
        
        return $xml;
    }
}
class Alternativa
{
    public $arrTramos;
    
    public function asXml()
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $alternativa = $xml->createElement("alternativa");
        $alternativa = $xml->appendChild($alternativa);
        
        foreach ($this->arrTramos as $tramo)
        {
            $xmlTramo = $tramo->asXml();
            
            $xmlArrTramo = $xmlTramo->getElementsByTagName("tramo");
            foreach($xmlArrTramo as $tramo) {
                $alternativa->appendChild($xml->importNode($tramo, true));
            }
            
        }
        
        return $xml;
    }
}
 class Tramo
{
    public $medioTransporte;
    public $distancia;
    public $corredor;
    public $ramal;
    public $zona;
    public $enlacesTramo;
    
    public function addEnlaceTramo($enlaceTramo) {
        $this->enlacesTramo[] = $enlaceTramo;
    }
    
    public function asXml() 
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $tramo = $xml->createElement("tramo");
        $tramo = $xml->appendChild($tramo);
        
        $medioTransporte = $xml->createElement("medioTransporte", $this->medioTransporte);
        $medioTransporte = $tramo->appendChild($medioTransporte);
        
        $distancia = $xml->createElement("distancia", $this->distancia);
        $distancia = $tramo->appendChild($distancia);
        
        $corredor = $xml->createElement("corredor", $this->corredor);
        $corredor = $tramo->appendChild($corredor);
        
        $ramal = $xml->createElement("ramal", $this->ramal);
        $ramal = $tramo->appendChild($ramal);
        
        $zona = $xml->createElement("zona", $this->zona);
        $zona = $tramo->appendChild($zona);
        
        
        foreach ($this->enlacesTramo as $enlace)
        {
            $xmlEnlace = $enlace->asXml();
            
            $xmlNodo = $xmlEnlace->getElementsByTagName("nodo");
            foreach($xmlNodo as $nodo) {
                $tramo->appendChild($xml->importNode($nodo, true));
            }
            
        }
        
        return $xml;
    }
}

 class EnlaceTramo
{
    public $id;
    public $latitud;
    public $longitud;
    public $descripcion = null;
    
    public function __construct($id, $latitud, $longitud)
    {
        $this->id = $id;
        $this->latitud = $latitud;
        $this->longitud = $longitud;
    }
    
    public function asXml() 
    {
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $nodo = $xml->createElement("nodo");
        $nodo = $xml->appendChild($nodo);
        
        $id = $xml->createElement("id", $this->id);
        $id = $nodo->appendChild($id);
        
        $latitud = $xml->createElement("latitud", $this->latitud);
        $latitud = $nodo->appendChild($latitud);
        
        $longitud = $xml->createElement("longitud", $this->longitud);
        $longitud = $nodo->appendChild($longitud);
        
        if (!is_null($this->descripcion)){
            $descripcion = $xml->createElement("descripcion", $this->descripcion);
            $descripcion = $nodo->appendChild($descripcion);
        }
        
        return $xml;
    }
}