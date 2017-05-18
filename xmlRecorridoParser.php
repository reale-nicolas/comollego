<?php
require_once "constantes.php";
require_once "ParadaOrden.php";

class xmlRecorridoParserParser {
    private $xmlName;
    private $xmlObject;
    
    private static $recorridosXmlList = Array(
        array("filename"=>"2A_back.xml",            "id"=>20, "linea"=>"2", "ramal"=>"A", "zona"=>""),
        array("filename"=>"3A_back.xml",            "id"=>30, "linea"=>"3", "ramal"=>"A", "zona"=>""),
        array("filename"=>"4A_BLS_back.xml",        "id"=>40, "linea"=>"4", "ramal"=>"A", "zona"=>"Barrio Los Sucres"),
        array("filename"=>"5A_SANSONE_back.xml",    "id"=>50, "linea"=>"5", "ramal"=>"A", "zona"=>"Sansone"),
        array("filename"=>"6A_back.xml",            "id"=>60, "linea"=>"6", "ramal"=>"A", "zona"=>""),
        array("filename"=>"6B_back.xml",            "id"=>61, "linea"=>"6", "ramal"=>"B", "zona"=>""),
        array("filename"=>"8A_back_reedit.xml",            "id"=>80, "linea"=>"8", "ramal"=>"A", "zona"=>"")
    );
    
    public function __construct() 
    {
        
    }
    
    public function getRecorridoLinea()
    {
        $arrCorredores = array();
        
        foreach (self::$recorridosXmlList as $corredoresDisponiblesList) 
        {
            $arrCorredores[] = $this->getRecorridoLineaByFilename($corredoresDisponiblesList["filename"]);
        }
        
        return $arrCorredores;
    }
    
    public function getRecorridoLineaById($id)
    {
        $bExiste = false;
        $arrCorredores = array();
        
        foreach (self::$recorridosXmlList as $corredoresDisponiblesList) 
        {
            if($corredoresDisponiblesList['id'] == $id)
            {
                $filename   = $corredoresDisponiblesList["filename"];
                $arrCorredores[] =  $this->getRecorridoLineaByFilename($filename);                
            }
        }
        
        if(!$bExiste) return null;
        
        return $arrCorredores;
    }
    
    public function getRecorridoLineaByCorredor($corredor)
    {
        foreach (self::$recorridosXmlList as $corredoresDisponiblesList) 
        {
            if($corredoresDisponiblesList['linea'] == $corredor)
            {
                $filename   = $corredoresDisponiblesList["filename"];
                return $this->getRecorridoLineaByFilename($filename);                
            }
        }
        
        if(!$bExiste) return null;
    }
    
    public function getRecorridoLineaByCorredorAndRamal($corredor, $ramal)
    {
        foreach (self::$recorridosXmlList as $corredoresDisponiblesList) 
        {
            if($corredoresDisponiblesList['linea'] == $corredor && $corredoresDisponiblesList['ramal'] == $ramal)
            {
                $filename   = $corredoresDisponiblesList["filename"];
                return $this->getRecorridoLineaByFilename($filename);                
            }
        }
        
        if(!$bExiste) return null;
    }
    
    public function getRecorridoLineaByFilename($filename)
    {
        $bExiste = false;
        $arrParadas = array();
        
        foreach (self::$recorridosXmlList as $corredoresDisponiblesList) 
        {
            if($corredoresDisponiblesList['filename'] == $filename)
            {
                $id =       $corredoresDisponiblesList["id"];
                $linea      = $corredoresDisponiblesList["linea"];
                $ramal      = $corredoresDisponiblesList["ramal"];
                $zona       = $corredoresDisponiblesList["zona"];
                $bExiste    = true;
            }
        }
        
        if(!$bExiste) return null;
                
        $this->xmlObject = simplexml_load_file(PATH_RECORRIDOS.$filename, null, LIBXML_NOCDATA);
        
        foreach($this->xmlObject->Document->Folder as $nodo)
        {
            if(strtoupper($nodo->name) == "PARADAS") {

                foreach($nodo->Placemark as $parada)
                {
                    $coordParada = split(",", $parada->Point->coordinates);
                    $parada->latitud = (double)$coordParada[1];
                    $parada->longitud = $coordParada[0];
                    
                    $arrParadas[] = $parada;
                } 
            }
        }
//        echo "<pre>";print_r($arrParadas);echo "</pre>";
        return Array (
            "id"        =>  $id,
            "linea"     =>  $linea,
            "ramal"     =>  $ramal,
            "zona"      =>  $zona,
            "paradas"   =>  $arrParadas
        );
    }
    
    
    public function getDescription($id){
        $descripcion = "punto de origen";
        foreach($this->xmlObject->Document->Folder as $nodo)
        {
            if(strtoupper($nodo->name) == "PARADAS") {

                foreach($nodo->Placemark as $parada)
                {
                    if ($parada->name == $id){
//                        $descripcion = trim(substr($parada->description, strpos($parada->description, "FID")+5, 5));
                        $descripcion = $parada->description;
                        break;
                    }
                } 
            }
        }
        return $descripcion;
    }
    public function edit()
    {        
        foreach($this->xmlObject->Document->Folder as $nodo)
        {
            if(strtoupper($nodo->name) == "PARADAS") {

                foreach($nodo->Placemark as $parada)
                {
                    $parada->name = (int)$parada->name + 1;
                } 
            }
        }
    }
    
    public function createFile($nameInputFile, $nameOutputFile, $nameArray)
    {
        $arrPuntosRepetidos = array();
        $this->xmlObject = new SimpleXMLElement(PATH_RECORRIDOS.$nameInputFile.".xml",null,true);
        $id=1;
        $arrParadas  = Array();
        
        foreach (ParadaOrden::$orden[$nameArray] as $nroParada) 
        {
            foreach($this->xmlObject->Document->Folder as $nodo)
            {
                if(strtoupper($nodo->name) == "PARADAS") {
                    $subindice = 0;
                    foreach($nodo->Placemark as $k => $parada)
                    {
                        if ($parada->name == "Punto ".$nroParada) 
                        {
                            $arrParadas[] = $parada;
                            
                            $arrPuntosRepetidos["'".$parada->name."'"]++;
                            break;
                        }
                        $subindice++;
                    } 
                }
            }
        }
        $i=0;
        
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $kml = $xml->createElement("kml");
        $kml->setAttribute("xmlns",'http://www.opengis.net/kml/2.2');
        $kml = $xml->appendChild($kml);
//        $folderDom = dom_import_simplexml($this->xmlObject->Document);
        $document = $xml->createElement("Document");
        $document = $kml->appendChild($document);
        
        foreach($this->xmlObject->Document as $elemento)
        {
            foreach ($elemento->Folder as $nodo)
            {
                if(strtoupper($nodo->name) != "PARADAS")
                {
                    $aaa = dom_import_simplexml($nodo);
                    
                    $folder = $xml->importNode($aaa, true);
                    $folder = $document->appendChild($folder);
                }
                
            }
        }
        
        $folderXML = simplexml_load_string("<Folder></Folder>");
        $folderDom = dom_import_simplexml($folderXML);
        
        $folder = $xml->importNode($folderDom);
        
        $folder = $document->appendChild($folder);
        
        $name = $xml->createElement("name","Paradas");
        $name = $folder->appendChild($name);
        foreach ($arrParadas as $parada)
        {
            $parada->description = $parada->name;
            $parada->name = $i+1;
            
            $aaa = dom_import_simplexml($parada);
                    
            $placeMark = $xml->importNode($aaa, true);
            $placeMark = $folder->appendChild($placeMark);
            
            $i++;
            
        }
        
        foreach($this->xmlObject->Document as $elemento)
        {
            foreach ($elemento->Style as $nodo)
            {
                    $aaa = dom_import_simplexml($nodo);
                    
                    $folder1 = $xml->importNode($aaa, true);
                    $folder = $document->appendChild($folder1);
            }
            foreach ($elemento->StyleMap as $nodo)
            {
                    $aaa = dom_import_simplexml($nodo);
                    
                    $folder1 = $xml->importNode($aaa, true);
                    $folder = $document->appendChild($folder1);
            }
        }
        
        header("Content-type: application/xml");
        
        $xml->formatOutput = true;
        $elXML = $xml->saveXML();
        $xml->save(PATH_RECORRIDOS.$nameOutputFile."_back.xml");
        echo $elXML;
       /* foreach($this->xmlObject->Document->Folder as $nodo)
        {
            if(strtoupper($nodo->name) == "PARADAS") {
                foreach($nodo->Placemark as $parada)
                {
                    if (isset($arrParadas[$i])) {
                        $parada->description = (string)$arrParadas[$i]->name;
                        $parada->name =  $i+1;                    
                        $parada->Point->coordinates = $arrParadas[$i]->Point->coordinates;
                        $parada->styleUrl = $arrParadas[$i]->styleUrl;
                        
                        $nodo->Placemark = $parada;
                    }
                    else {
                        unset($parada);
                    }
                    $i++;
                } 
            } 
        }*/
//        echo "<pre>";var_dump($arrPuntosRepetidos);echo "</pre>";
//        $this->xmlObject->saveXML(PATH_RECORRIDOS.$nameOutputFile."_back.xml");
    }
    /**
     * @return 
     */
    public function getArrParadas() {
        return $this->arrParadas;
    }
    
    
    
    public function reEditFileIds($nameInputFile, $nameOutputFile)
    {
        $arrPuntosRepetidos = array();
        $this->xmlObject = new SimpleXMLElement(PATH_RECORRIDOS.$nameInputFile.".xml",null,true);
        $id=1;
        $arrParadas  = Array();
        $i=0;
        foreach($this->xmlObject->Document->Folder as $nodo)
        {
            if(strtoupper($nodo->name) == "PARADAS") 
                
                foreach($nodo->Placemark as $k => $parada)
                {
                    $arrParadas[] = $parada;
                    $parada->description = (substr_count(strtoupper($parada->name), "PUNTO"))?$parada->name:$parada->description;
                    $parada->name = $i+1;
                    $i++;
                } 
        }
        
         header("Content-type: application/xml");
        
        $this->xmlObject->formatOutput = true;
        $elXML = $this->xmlObject->saveXML();
//        $this->xmlObject->save(PATH_RECORRIDOS.$nameOutputFile."_back1123.xml");
        echo $elXML;
        
        
        /*$i=0;
        
        $xml = new DOMDocument("1.0", "UTF-8");
        
        $kml = $xml->createElement("kml");
        $kml->setAttribute("xmlns",'http://www.opengis.net/kml/2.2');
        $kml = $xml->appendChild($kml);
//        $folderDom = dom_import_simplexml($this->xmlObject->Document);
        $document = $xml->createElement("Document");
        $document = $kml->appendChild($document);
        
       
        foreach ($arrParadas as $parada)
        {
            $parada->description = (substr_count(strtoupper($parada->name), "PUNTO"))?$parada->name:$parada->description;
            $parada->name = $i+1;
            
            $aaa = dom_import_simplexml($parada);
                    
            $placeMark = $xml->importNode($aaa, true);
            $placeMark = $folder->appendChild($placeMark);
            
            $i++;
            
        }
        
        foreach($this->xmlObject->Document as $elemento)
        {
            foreach ($elemento->Style as $nodo)
            {
                    $aaa = dom_import_simplexml($nodo);
                    
                    $folder1 = $xml->importNode($aaa, true);
                    $folder = $document->appendChild($folder1);
            }
            foreach ($elemento->StyleMap as $nodo)
            {
                    $aaa = dom_import_simplexml($nodo);
                    
                    $folder1 = $xml->importNode($aaa, true);
                    $folder = $document->appendChild($folder1);
            }
        }
        
        header("Content-type: application/xml");
        
        $xml->formatOutput = true;
        $elXML = $xml->saveXML();
        $xml->save(PATH_RECORRIDOS.$nameOutputFile."_back1123.xml");
        echo $elXML;*/
       
    }
}