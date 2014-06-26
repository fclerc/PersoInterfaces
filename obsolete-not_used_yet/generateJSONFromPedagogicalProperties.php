<?php
//ABANDONED : EASIER WAY TO  DISPLAY IT DIRECTLY WITH JAVASCRIPT (as this file is nnit hard to parse)


//This script generates as much documentation as possible in a json object form the annotations present in a XML Schema.
//generateJSONFromXMLSchema('context.xsd', 'contextScales.json');

$t = new JSONFromPedagogicalPropertiesGenerator();
$t->generateJSONFromDoc('../data/schemas/foveaPedagogicalProperties.xml', '../data/schemas/parametersDocumentation.json');

class JSONFromPedagogicalPropertiesGenerator{
    private $dictionnary;
    public function __construct(){
        $this->dictionnary = array();
    }
    
    public function generateJSONFromDoc($propertiesDoc, $outputFile){

        $xml = new DOMDocument();
        $xml->load($propertiesDoc);
        $parameters = $xml->getElementsByTagName('parameter');
        

        foreach ($parameters as $parameter) {
            $parameterId = $parameter->getAttribute('id');
            if(!array_key_exists($name, $this->dictionnary)){//parameter not yet added
                $this->dictionnary[$name] = array();
                $documentation = $parameter->getElementsByTagName('Comment')->item(0);
                echo $documentation;
            }
                
        }
        $json = json_encode($this->dictionnary);
        echo $json;
        //file_put_contents($outputFile, $json);

    }
    
}
?>

