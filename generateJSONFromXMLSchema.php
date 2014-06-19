<?php

$schemaFileName = 'learnerMoocProfile.xsd';
$outputFile = 'profileScales.json';

$xml = new DOMDocument();
$xml->load($schemaFileName);
$elements = $xml->getElementsByTagName('element');
$xpath = new DOMXpath($xml);//used for more complex queries
$dictionnary = array();

foreach ($elements as $element) {
    $name = $element->getAttribute('name');
    if(!array_key_exists($name, $dictionnary)){//element not yet added
        $dictionnary[$name] = array("rien" => "rien");
        $documentations = $element->getElementsByTagName('documentation');
        if($documentations->length > 0){
            $dictionnary[$name]["documentation"] = $documentations->item(0)->textContent;;
        }
        
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
                    $dictionnary[$name]['nature'] = 'predefined';
                    $dictionnary[$name]['typeName'] = $type;
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
        //$dictionnary[$name] = array('nature' => 'restriction', 'baseTypeName' => $restriction->getAttribute('base'));
        $dictionnary[$name]['nature'] = 'restriction';
        $dictionnary[$name]['baseTypeName'] = $restriction->getAttribute('base');
        
        
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
    //for documentation, we also look in the type definition itself.
    $documentations = $simpleTypeElement->getElementsByTagName('documentation');
    foreach($documentations as $documentation){
        if(isset($dictionnary[$name]['documentation'])){//if something is already contained, put a new line
            $dictionnary[$name]['documentation'] += '<br/>';
            $dictionnary[$name]['documentation'] += $documentation->textContent;
        }
        else{
            $dictionnary[$name]['documentation'] = $documentation->textContent;
        }
    }
    
    
    
    //TODO if necessary : treat other simple types
}

//TODO : also treat the attributes


$json = json_encode($dictionnary);
echo $json;
file_put_contents($outputFile, $json);

?>





















