<?php
//This script generates documentation in a json object form the annotations present in a XML Schema. Also enables  to know more about the data types to use for the elements (these informations are then used to help the user when he does tasks like defining his strategy)

//use next  lines to generate the documentation for the schema you want
$t = new JSONFromXMLSchemaGenerator();
//$t->generateJSONFromXMLSchema('../data/models/context.xsd', '../data/infos/contextScales.json');
$t->generateJSONFromXMLSchema('../data/models/learnerMoocProfile.xsd', '../data/infos/profileScales.json');

class JSONFromXMLSchemaGenerator{
    private $dictionnary;//the array that will contain all the informations extracted
    public function __construct(){
        $this->dictionnary = array();
    }
     //$resourcesDoc : full path to the schema file; $outputFile = full path for the JSON you want to create
    public function generateJSONFromXMLSchema($schemaFileName, $outputFile){

        $xml = new DOMDocument();
        $xml->load($schemaFileName);
        $xpath = new DOMXpath($xml);//used for more complex queries
        $elements = $xml->getElementsByTagName('element');
        
        //going through all the elements, and getting doc about them. Informations are diverse and can be foind in different places of the document; depending on whether it is a simple or complex type, whether the type is defined directly with the element or somewhere else,...
        foreach ($elements as $element) {
            $name = $element->getAttribute('name');
            if(!array_key_exists($name, $this->dictionnary)){//element not yet added
                $this->dictionnary[$name] = array();
                
                //getting the documentation inside the element itself. Using a xpath expression to be sure you only get the documentation concerning this element (and not the documentation that could belong to its children)
                $searchString = 'xs:annotation/xs:documentation';
                $docContainer = $xpath->query($searchString, $element);
                foreach($docContainer as $doc){
                    if(isset( $this->dictionnary[$name]["documentation"])){
                        $this->dictionnary[$name]["documentation"] += $doc->textContent;
                    }
                    else{
                        $this->dictionnary[$name]["documentation"] = $doc->textContent;
                    }
                }
                
                
                if($element->hasAttribute('type')){//we have to find the type definition somewhere else, or it is a pre-defined type
                    $type = $element->getAttribute('type');
                    //xpath expression to see if the type is complex and defined somewhere else
                    $searchString = "count(//xs:complexType[@name='".$type."'])";
                    $complexTypeIsDefined = $xpath->evaluate($searchString);
                    
                    
                    if($complexTypeIsDefined){//this is a complex type, defined somewhere in the document, do nothing yet but getting the documentation
                        $searchString = "//xs:complexType[@name='".$type."']";
                        $complexTypeContainer = $xpath->query($searchString);
                        foreach($complexTypeContainer as $complexType){
                            $this->treatComplexType($name, $complexType, $xpath);
                        }
                    }
                    
                    else{//this is a simple type
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
                    else{//this is a simple type
                        $this->treatSimpleType($name, $element->getElementsByTagName('simpleType')->item(0));
                    }
                
                }
                
                
                //removing all the \t from the documentation
                if(isset( $this->dictionnary[$name]["documentation"])){
                    $this->dictionnary[$name]["documentation"] = str_replace("\t", '', $this->dictionnary[$name]["documentation"]);
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
    
    
    private function treatComplexType($name, $complexType, $xpath){
        //getting documentation
        $searchString = 'xs:annotation/xs:documentation';
        $docContainer = $xpath->query($searchString, $complexType);
        foreach($docContainer as $doc){
            if(isset( $this->dictionnary[$name]["documentation"])){
                $this->dictionnary[$name]["documentation"] += $doc->textContent;
            }
            else{
                $this->dictionnary[$name]["documentation"] = $doc->textContent;
            }
        }
    
    }
    
}
?>

