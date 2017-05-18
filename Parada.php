<?php
require_once "Nodo.php";
require_once "NodoTipo.php";

class Parada extends Nodo 
{
    private $id;
    private $name;
    
    private $recorridoColectivo;
    
    public function __construct($id, $name, $latitud, $longitud) 
    {
        parent::__construct($latitud, $longitud, NodoTipo::$PARADA);
        $this->setId($id);
        $this->setName($name);
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
    /**
     * @param  $name
     */
    public function setName($name) {
        $this->name = $name;
    }
    /**
     * @return 
     */
    public function getName() {
        return $this->name;
    }
    
    
    /**
     * @param  $recorridoColectivo
     */
    public function setRecorridoColectivo($recorridoColectivo) {
        $this->recorridoColectivo = $recorridoColectivo;
    }
    /**
     * @return 
     */
    public function getRecorridoColectivo() {
        return $this->recorridoColectivo;
    }
}
