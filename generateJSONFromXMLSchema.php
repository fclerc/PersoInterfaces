<?php

$schemaFileName = 'context.xsd';
$outputFile = 'contextScales.json';

$xml = new DOMDocument();
$xml->load($schemaFileName);
$elements = $xml->getElementsByTagName('element');
$xpath = new DOMXpath($xml);//used for more complex queries
$dictionnary = array();

foreach ($elements as $element) {
    $name = $element->getAttribute('name');
    if(!array_key_exists($name, $dictionnary)){//element not yet added
        if($element->hasAttribute('type')){//we have to find the type definition somewhere else, or it is a pre-defined type
            $type = $element->getAttribute('type');
            //xpath expression to see if the type is complex and defined somewhere else
            $searchString = "count(//xs:complexType[@name='".$type."'])";
            $complexTypeIsDefined = $xpath->evaluate($searchString);
            
            
            if($complexTypeIsDefined){//this is a complex type, defined somewhere in the document, do nothing yet
            
            
            }
            else{
                $searchString = "count(//xs:simpleType[@name='".$type."'])";
                $simpleTypeIsDefined = $xpath->evaluate($searchString);
                if($simpleTypeIsDefined){//this is a simple type, defined somewhere in the document
                    $searchString = "//xs:simpleType[@name='".$type."']";
                    $simpleTypeContainer = $xpath->query($searchString);
                    foreach($simpleTypeContainer as $simpleType){
                        treatSimpleType($name, $simpleType);
                    }
                }
                else{//this is a predefined type
                    $dictionnary[$name] = array('nature' => 'predefined', 'typeName' => $type);
                }
            
            
            }
            
            
            
            
            
            
        }
        
        else{//the type is inside this element
            
            if($element->getElementsByTagName('complexType')->length){//the type is complex, do nothing yet
                
            }
            else{//this is a simple type TODO
                treatSimpleType($name, $element->getElementsByTagName('simpleType')->item(0));
            }
        
        }
    }
    
}

function treatSimpleType($name, $simpleTypeElement){
    global $dictionnary;
    //case the simple type is based on restrictions
   $restrictions = $simpleTypeElement->getElementsByTagName('restriction');
   foreach($restrictions as $restriction){
        $dictionnary[$name] = array('nature' => 'restriction', 'baseTypeName' => $restriction->getAttribute('base'));
        
        //getting the min and max in case it contains such an element
        $mins = $restriction->getElementsByTagName('minInclusive');
        $maxs = $restriction->getElementsByTagName('maxInclusive');
        foreach($mins as $min){
            $dictionnary[$name]['min'] = $min->getAttribute('value');
        }
        foreach($maxs as $max){
            $dictionnary[$name]['max'] = $max->getAttribute('value');
        }
        
        //if enumeration : get the items
        $enumeration = array();
        $enums = $restriction->getElementsByTagName('enumeration');
        foreach($enums as $enum){
            $enumeration[]= $enum->getAttribute('value');
        }
        if(sizeOf($enumeration)){
            $dictionnary[$name]['enumeration'] = $enumeration;
        }
    }
    
    
    
    
    
    //TODO if necessary : treat other simple types
}

//TODO : also treat the attributes


$json = json_encode($dictionnary);
echo $json;
file_put_contents($outputFile, $json);

?>





















