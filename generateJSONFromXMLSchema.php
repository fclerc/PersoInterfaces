<?php



//$t = array('ivi' => array('a' => 'b'), 'ijbv' => 125);
$xml = new DOMDocument();
$xml->load('learnerMoocProfile.xsd');
$elements = $xml->getElementsByTagName('element');
$xpath = new DOMXpath($xml);//used for more complex queries
$dictionnary = array();

foreach ($elements as $element) {
    $name = $element->getAttribute('name');
    if(!array_key_exists($name, $dictionnary)){//element not yet added
        if($element->hasAttribute('type')){//we have to find the type somewhere else, or it is a pre-defined type
            $type = $element->getAttribute('type');
            //xpath expression to see if the type is complex and defined somewhere else
            $searchString = "count(//xs:complexType[@name='".$type."'])";
            
            $complexTypeIsDefined = $xpath->evaluate($searchString);
            
            
            if($complexTypeIsDefined){//this is a complex type, defined somewhere in the document
            
            
            }
            else{
                $searchString = "count(//xs:simpleType[@name='".$type."'])";
                $simpleTypeIsDefined = $xpath->evaluate($searchString);
                if($simpleTypeIsDefined){//this is a simple type, defined somewhere in the document
            
                }
                else{//this is a predefined type
                
                
                    $dictionnary[$name] = array('nature' => 'predefined', 'typeName' => $type);
                }
            
            
            }
            
            
            
            
            
            
        }
        
        else{//the type is inside this element
            if(count($element->getElementsByTagName('complexType'))){//the type is complex, do nothing yet
                
            }
            else{//this is a simple type TODO
            
            }
        
        }
    }
    
}

//TODO : also treat the attributes


$json = json_encode($dictionnary);
echo $json;
file_put_contents('profileScales.json', $json);
//$array = json_decode($json,TRUE);
















?>





















