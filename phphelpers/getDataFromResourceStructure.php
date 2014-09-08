<?php
//Goal is to extract, for each parameter, a list of the values the user indicated when defining the resources.
//Even if some attributes are depending on lists  (restrictions in xml schema) that are already defined in schemas or other files, we store here all the values that the user actually gave to its resources when defining them (might help him to provide better support, by showing only the values that are useful to him, and could also help detecting errors).

//Automatically used when user stores a new xml for resources.

//use the 2 following lines to generate the data from the file you want
$t = new getDataFromResourceStructure();
//$t->generateJSONFromDoc('../data/resources/foveaResources.xml', '../data/infos/resourcesData.json');

class getDataFromResourceStructure{
    private $dictionnary;//the array that will contain all the data
    public function __construct(){
        $this->dictionnary = array();
    }
    //$resourcesDoc : full path to the resources file; $outputFile = full path for the JSON you want to create
    public function generateJSONFromDoc($resourcesDoc, $outputFile){
        $xml = new DOMDocument();
        $xml->load($resourcesDoc);
        
        //the parameters for which we want to store the values
        $parameters = array('name', 'type', 'status', 'context', 'difficulty', 'sequence', 'categories');

        foreach ($parameters as $parameter) {//going through all the resources tags for which we want to get the values
            $this->dictionnary[$parameter] = array();
            
            $valueContainers = $xml->getElementsByTagName($parameter);//getting all the tags having this tagname
            foreach($valueContainers as $valueContainer){//for all values, storing only those that aren't in the array yet
                $value = $valueContainer->nodeValue;
                
                if($parameter == 'context' || $parameter == 'categories'){//for these 2 parameters there might be several values, separated by spaces
                    $values = explode(' ', $value);
                    foreach($values as $value2){
                        if(array_search($value2, $this->dictionnary[$parameter]) === false){//value not in the array
                            $this->dictionnary[$parameter][] = $value2;
                        }
                    }
                
                }
                
                //a simple value that is not in the array
                else if(array_search($value, $this->dictionnary[$parameter]) === false){
                    $this->dictionnary[$parameter][] = $value;
                }
            }
                
        }
        
        //displaying the result and storing it
        $json = json_encode($this->dictionnary);
        echo $json;
        file_put_contents($outputFile, $json);

    }
    
}
?>

