<?php
//This script generates as much documentation as possible in a json object form the annotations present in a XML Schema.
//generateJSONFromXMLSchema('context.xsd', 'contextScales.json');

$t = new JSONFromXMLSchemaGenerator();
$t->generateJSONFromXMLSchema('context.xsd', 'contextScales.json');

class JSONFromXMLSchemaGenerator{
    private $dictionnary;
    public function __construct(){
        $this->dictionnary = array();
    }
    
    public function generateJSONFromXMLSchema($schemaFileName, $outputFile){

        $xml = new DOMDocument();
        $xml->load($schemaFileName);
        $elements = $xml->getElementsByTagName('element');
        $xpath = new DOMXpath($xml);//used for more complex queries

        foreach ($elements as $element) {
            $name = $element->getAttribute('name');
            if(!array_key_exists($name, $this->dictionnary)){//element not yet added
                $this->dictionnary[$name] = array();
                $documentations = $element->getElementsByTagName('documentation');
                if($documentations->length > 0){
                    $this->dictionnary[$name]["documentation"] = $documentations->item(0)->textContent;;
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
                                $this->treatSimpleType($name, $simpleType);
                            }
                        }
                        else{//this is a predefined type
                            $this->dictionnary[$name]['nature'] = 'predefined';
                            $this->dictionnary[$name]['typeName'] = $type;
                        }
                    
                    
                    }
                    
                }
                
                else{//the type is inside this element
                    
                    if($element->getElementsByTagName('complexType')->length){//the type is complex, do nothing yet
                        
                    }
                    else{//this is a simple type TODO
                        $this->treatSimpleType($name, $element->getElementsByTagName('simpleType')->item(0));
                    }
                
                }
            }
            
        }

        

        //TODO : also treat the attributes


        $json = json_encode($this->dictionnary);
        echo $json;
        file_put_contents($outputFile, $json);
    }
    
    private function treatSimpleType($name, $simpleTypeElement){
        //case the simple type is based on restrictions
       $restrictions = $simpleTypeElement->getElementsByTagName('restriction');
       foreach($restrictions as $restriction){
            //$this->dictionnary[$name] = array('nature' => 'restriction', 'baseTypeName' => $restriction->getAttribute('base'));
            $this->dictionnary[$name]['nature'] = 'restriction';
            $this->dictionnary[$name]['baseTypeName'] = $restriction->getAttribute('base');
            
            
            //getting the min and max in case it contains such an element
            $mins = $restriction->getElementsByTagName('minInclusive');
            $maxs = $restriction->getElementsByTagName('maxInclusive');
            foreach($mins as $min){
                $this->dictionnary[$name]['min'] = $min->getAttribute('value');
            }
            foreach($maxs as $max){
                $this->dictionnary[$name]['max'] = $max->getAttribute('value');
            }
            
            //if enumeration : get the items
            $enumeration = array();
            $enums = $restriction->getElementsByTagName('enumeration');
            foreach($enums as $enum){
                $enumeration[]= $enum->getAttribute('value');
            }
            if(sizeOf($enumeration)){
                $this->dictionnary[$name]['enumeration'] = $enumeration;
            }
        }
        //for documentation, we also look in the type definition itself.
        $documentations = $simpleTypeElement->getElementsByTagName('documentation');
        foreach($documentations as $documentation){
            if(isset($this->dictionnary[$name]['documentation'])){//if something is already contained, put a new line
                $this->dictionnary[$name]['documentation'] += '<br/>';
                $this->dictionnary[$name]['documentation'] += $documentation->textContent;
            }
            else{
                $this->dictionnary[$name]['documentation'] = $documentation->textContent;
            }
        }
        
        
        
        //TODO if necessary : treat other simple types
    }
    
}
?>

