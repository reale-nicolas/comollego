<?PHP 


class Dijkstra 
{   
    var $visited = array(); 
    var $distance = array(); 
    var $previousNode = array(); 
    var $startnode =null; 
    var $map = array();
    var $infiniteDistance = 0; 
    var $numberOfNodes = 0; 
    var $bestPath = 0; 
    var $matrixWidth = 0;  
    public $mejorRuta = array();
    

    function Dijkstra(&$ourMap, $infiniteDistance) 
    { 
        $this -> infiniteDistance = $infiniteDistance; 
        $this -> map = &$ourMap; 
        $this -> numberOfNodes = count($ourMap) ; 
        $this -> bestPath = 0; 
    }   
    
    
    function findShortestPath($start, $to = null) 
    { 
        $this -> startnode = $start; 
        for ($i=0; $i<$this -> numberOfNodes; $i++) 
        { 
            if ($i == $this -> startnode) 
            { 
                $this -> visited[$i] = true; 
                $this -> distance[$i] = 0; 
            } 
            else 
            { 
                $this -> visited[$i] = false; 
                $this -> distance[$i] = isset($this -> map[$this -> startnode][$i])  ? $this -> map[$this -> startnode][$i]  : $this -> infiniteDistance; 
            } 
            
            $this -> previousNode[$i] = $this -> startnode; 
        }
        
        $maxTries = $this -> numberOfNodes; 
        $tries = 0; 
        
        while (in_array(false, $this -> visited, true) && $tries <= $maxTries) 
        {
            $this -> bestPath = $this->findBestPath($this->distance, array_keys($this -> visited, false, true));
            if($to !== null && $this -> bestPath === $to) { 
                break; 
            } 
            
            $this -> updateDistanceAndPrevious($this -> bestPath); 
            $this -> visited[$this -> bestPath] = true; 
            $tries++; 
        }
    } 
  
  
    function findBestPath($ourDistance, $ourNodesLeft) 
    { 
        $bestPath = $this -> infiniteDistance; 
        $bestNode = 0; 
        
        for ($i = 0, $m = count($ourNodesLeft); $i < $m; $i++) 
        { 
            if($ourDistance[$ourNodesLeft[$i]] < $bestPath) 
            { 
                $bestPath = $ourDistance[$ourNodesLeft[$i]]; 
                $bestNode = $ourNodesLeft[$i]; 
            } 
        } 
        
        return $bestNode; 
    } 
      
      
    function updateDistanceAndPrevious($obp) 
    { 
        for ($i = 0; $i < $this->numberOfNodes; $i++) 
        { 
            if( (isset($this->map[$obp][$i]))   && 
                (!($this->map[$obp][$i] == $this->infiniteDistance) || ($this->map[$obp][$i] == 0 )) && 
                (($this->distance[$obp] + $this->map[$obp][$i]) < $this -> distance[$i]) )  
            { 
                $this -> distance[$i] = $this -> distance[$obp] + $this -> map[$obp][$i]; 
                $this -> previousNode[$i] = $obp; 
            } 
        } 
    }   


    function printMap(&$map) { 
        $placeholder = ' %' . strlen($this -> infiniteDistance) .'d'; 
        $foo = ''; 
        $linea = '';
        for($i=0,$im=count($map);$i<$im;$i++) { 
            $linea = '';
            $cabecera = '';
            $separador = '';
            for ($k=0,$m=$im;$k<$m;$k++) { 
                $linea .= sprintf($placeholder, isset($map[$i][$k]) ? $map[$i][$k] : $this -> infiniteDistance); 
                $cabecera .= sprintf($placeholder, $k);
                $separador .= sprintf(' %' . strlen($this -> infiniteDistance) .'s', "_");
            } 
            
            $foo.=  sprintf('%2d  |', $i).$linea." \n"; 
        } 
        $cabecera .= " \n";
        $separador .= " \n";
        return $cabecera.$separador.$foo; 
    }


    function getResults($to = null) 
    {
        $ourShortestPath = array(); 
        $foo = ''; 
        
        for ($i = 0; $i < $this -> numberOfNodes; $i++) 
        { 
            if($to !== null && (int)$to !== (int)$i)  
                continue; 
            
            $ourShortestPath[$i] = array(); 
            $endNode = null; 
            $currNode = $i; 
            $ourShortestPath[$i][] = $this -> previousNode[$currNode]; 
            
            while ($endNode === null || $endNode != $this -> startnode) 
            {
                $ourShortestPath[$i][] = $this -> previousNode[$currNode]; 
                $endNode = $this -> previousNode[$currNode];
                $currNode = $this -> previousNode[$currNode]; 
            }
 
            $ourShortestPath[$i][0] = $to; 
            $ourShortestPath[$i] = array_reverse($ourShortestPath[$i]); 
            
            $this->mejorRuta[$this -> startnode][$i] = Array("distancias"               =>  $this -> distance[$i], 
                                                             "numeroNodosatravezados"   =>  count($ourShortestPath[$i]), 
                                                             "nodos"                    =>  $ourShortestPath[$i]);            
            if ((int)$to === null || (int)$to === (int)$i) 
            { 
                if($this -> distance[$i] >= $this -> infiniteDistance) 
                { 
                    $this->mejorRuta[$this -> startnode][$i] = null;
                    $foo .= sprintf("no route from %d to %d. \n",$this -> startnode,$i); 
                } 
                else 
                {  
                    $foo .= sprintf('%d => %d = %d [%d]: (%s).'."\n" , $this -> startnode,$i,$this -> distance[$i], count($ourShortestPath[$i]), implode('-',$ourShortestPath[$i])); 
                } 
                
                $foo .= str_repeat('-',20) . "\n"; 
                if ($to === $i) 
                { 
                    break; 
                } 
            }
        } 
        return $this->mejorRuta; 
    }
} 
?>