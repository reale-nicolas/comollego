<?php
class PuntoGeografico 
{
    
    private $latitud;
    private $longitud;
    
    public function __construct($latitud, $longitud) 
    {
        $this->setLatitud($latitud);
        $this->setLongitud($longitud);
    }
    
    
    public function dentroDelRadio($puntoGeografico, $radio)
    {
        $distanciaEntrePuntos = distancia(
                                            $this->getLatitud(), 
                                            $this->getLongitud(), 
                                            $puntoGeografico->getLatitud(), 
                                            $puntoGeografico->getLongitud(), 
                                            "m", 
                                            2
        );
        
        if($distanciaEntrePuntos <= $radio)
            return true;
            
        return false;
    }
    /**
     * @param  $latitud
     */
    public function setLatitud($latitud) {
        $this->latitud = $latitud;
    }
    /**
     * @return 
     */
    public function getLatitud() {
        return $this->latitud;
    }
    /**
     * @param  $longitud
     */
    public function setLongitud($longitud) {
        $this->longitud = $longitud;
    }
    /**
     * @return 
     */
    public function getLongitud() {
        return $this->longitud;
    }
   
}
