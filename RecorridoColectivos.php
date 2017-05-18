<?php


class RecorridoColectivos 
{
    private $id;
    private $corredor;
    private $ramal;
    private $zona;
    
    private $paradas = array();
    
    public function __construct($id, $corredor, $ramal, $zona) 
    {
        $this->setId($id);
        $this->setCorredor($corredor);
        $this->setRamal($ramal);
        $this->setZona($zona);
    }
    
    
    public function addParada($parada)
    {
        $this->paradas[] = $parada;
    }
    
    public function getParadas()
    {
        return $this->paradas;
    }
    
    public function nodoPerteneceARecorrido($nodoId)
    {
        foreach ($this->getParadas() as $parada)
        {
            if($parada->getIdDikjstra() == $nodoId)
            {
                return true;
                break;
            }
        }
        
        return false;
    }
    /**
     * @param  $corredor
     */
    public function setCorredor($corredor) {
        $this->corredor = $corredor;
    }
    /**
     * @return 
     */
    public function getCorredor() {
        return $this->corredor;
    }
    /**
     * @param  $ramal
     */
    public function setRamal($ramal) {
        $this->ramal = $ramal;
    }
    /**
     * @return 
     */
    public function getRamal() {
        return $this->ramal;
    }
    /**
     * @param  $zona
     */
    public function setZona($zona) {
        $this->zona = $zona;
    }
    /**
     * @return 
     */
    public function getZona() {
        return $this->zona;
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
}
