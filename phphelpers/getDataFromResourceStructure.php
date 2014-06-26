<?php
//Goal is to extract lists from the resources about the different parameters that can be used when defining activities
//Even if some attributes are depending on lists that are defined in schemas or other files, we store here all the values that the user actually gave to its resources when defining them (might help him to provide better support, by shiwung only the values that are useful to him, and could also help detecting errors).

$t = new getDataFromResourceStructure();
$t->generateJSONFromDoc('../data/resources/foveaResources.xml', '../data/schemas/resourcesData.json');

class getDataFromResourceStructure{
    private $dictionnary;
    public function __construct(){
        $this->dictionnary = array();
        // $this->dictionnary['name'] = array();
        // $this->dictionnary['type'] = array();
        // $this->dictionnary['status'] = array();
        // $this->dictionnary['context'] = array();
        // $this->dictionnary['difficulty'] = array();
        // $this->dictionnary['sequence'] = array();
        // $this->dictionnary['categories'] = array();
    }
    
    public function generateJSONFromDoc($resourcesDoc, $outputFile){
        $xml = new DOMDocument();
        $xml->load($resourcesDoc);
        
        $parameters = array('name', 'type', 'status', 'context', 'difficulty', 'sequence', 'categories');

        foreach ($parameters as $parameter) {//going through all the resources tags for which we want to get the values
            $this->dictionnary[$parameter] = array();
            
            $valueContainers = $xml->getElementsByTagName($parameter);//getting all the tags having this tagname
            foreach($valueContainers as $valueContainer){//for all values, storing only those that aren't in the array yet
                $value = $valueContainer->nodeValue;
                
                if($parameter == 'context' || $parameter == 'categories'){//there might be several values, separated by spaces
                    $values = explode(' ', $value);
                    foreach($values as $value2){
                        if(array_search($value2, $this->dictionnary[$parameter]) === false){//value not in the array
                            $this->dictionnary[$parameter][] = $value2;
                        }
                    }
                
                }
                
                else if(array_search($value, $this->dictionnary[$parameter]) === false){//value not in the array
                    $this->dictionnary[$parameter][] = $value;
                }
            }
                
        }
        $json = json_encode($this->dictionnary);
        echo $json;
        file_put_contents($outputFile, $json);

    }
    
}
?>

