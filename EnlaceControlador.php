<?php


class EnlaceControlador 
{
    private $enlaces = array();
    
    public function __construct($enlaces = null) 
    {
        $this->setEnlaces($enlaces);
    }
    
    
    public function getEnlace()
    {
        $nroArgs = func_num_args();
        
        if($nroArgs == 2) 
        {
            if (is_a(func_get_arg(0), "PuntoGeografico") && is_a(func_get_arg(1), "PuntoGeografico"))
                return $this->getEnlaceByPuntoGeografico1AndPuntoGeografico2(func_get_arg(0), func_get_arg(1));
            else if (gettype(func_get_arg(0)) == "integer" && gettype(func_get_arg(1)) == "integer")
                return $this->getEnlaceByPuntoGeografico1IdAndPuntoGeografico2Id(func_get_arg(0), func_get_arg(1));
        } 
        else if($nroArgs == 1) 
        {
            if (is_a(func_get_arg(0), "PuntoGeografico"))
                return $this->getEnlaceByPuntoGeografico(func_get_arg(0));
            else if (gettype(func_get_arg(0)) == "integer")
                return $this->getEnlaceByPuntoGeograficoId(func_get_arg(0));
        } 
        else 
        {
            echo "Error en el numero de argumentos";
        }
        return null;
            
        //echo "El numero de argumentos es ".$nroArgs."<br>El primero <pre>";print_r(func_get_arg(0));echo "</pre><br>Y el segundo: <pre>";
        //print_r(func_get_arg(1));echo "</pre>";
    }
    
    
    public function getEnlaceByPuntoGeografico1AndPuntoGeografico2($puntoGeografico1, $puntoGeografico2)
    {
        
        if(
            ( get_class($puntoGeografico1) == "PuntoGeografico" || get_class($puntoGeografico1) == "Parada")
            &&
            ( get_class($puntoGeografico2) == "PuntoGeografico" || get_class($puntoGeografico2) == "Parada")
        )
        {
            foreach ($this->getEnlaces() as $enlace) 
            {
                if (($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeografico1->getIdDikjstra()
                    &&
                    $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeografico2->getIdDikjstra()) 
                    ||
                    ($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeografico2->getIdDikjstra()
                    &&
                    $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeografico1->getIdDikjstra()) 
                    ) 
                {
                    return $enlace;
                    break;
                }
            }
            
        }  else {
            echo "Error en el tipo de argumetnos";
        }
        
        return null;
    }
    
    public function getEnlaceByPuntoGeografico($puntoGeografico) 
    {
        if(get_class($puntoGeografico) == "PuntoGeografico" || get_class($puntoGeografico) == "Parada")
        {
            foreach ($this->getEnlaces() as $enlace) 
            {
                if (($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeografico->getIdDikjstra()
                    ||
                    $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeografico->getIdDikjstra())
                    ) 
                {
                    return $enlace;
                    break;
                }
            }
            
        } 
        
        return null;
    }
    
    public function getEnlaceByPuntoGeograficoId($puntoGeograficoId) 
    {
        $arrEnlaces = array();
        foreach ($this->getEnlaces() as $enlace) 
        {
            if ($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeograficoId
                ||
                $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeograficoId
                ) 
            {
                $arrEnlaces[] = $enlace;
                
            }
        }
        
        return $arrEnlaces;
    }
    
    public function getEnlaceByPuntoGeografico1IdAndPuntoGeografico2Id($puntoGeografico1Id, $puntoGeografico2Id) 
    {
        $arrEnlaces = array();
        
        foreach ($this->getEnlaces() as $enlace) 
        {
            if (($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeografico1Id
                &&
                $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeografico2Id)
                ||
                ($enlace->getPuntoGeografico1()->getIdDikjstra() == $puntoGeografico2Id
                &&
                $enlace->getPuntoGeografico2()->getIdDikjstra() == $puntoGeografico1Id)
                ) 
            {
                $arrEnlaces = $enlace;
                
            }
        }
        
        return $arrEnlaces;
    }
    
    
    /**
     * @param  $enlaces
     */
    public function setEnlaces($enlaces) {
        $this->enlaces = $enlaces;
    }
    /**
     * @return 
     */
    public function getEnlaces() {
        return $this->enlaces;
    }
    
    /**
     * @param  $enlaces
     */
    public function addEnlace($enlace) {
        $this->enlaces[] = $enlace;
    }
}