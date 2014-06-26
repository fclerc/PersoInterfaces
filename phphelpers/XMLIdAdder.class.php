<?php

    /*
        Class to add recursively ids to tags in xml document.
        
        
        Example of use :
        
        
        include 'XMLIdAdder.class.php';
        $x = new XMLIdAdder();
        $x->addIdsToXML('file.xml', 'youPrefix', 'yourSuffix', 'all', 0);
        
        It will generate id attributes to all tags : youPrefixNByourSuffix, with NB an incremented number.
        Use 'leaves' as last arguments if you only want to add ids to the leaves (tags having no child).
    
    */




    class XMLIdAdder{
    
        private $id = 0; //the id will be iterated over all tags TODO : if ids already exist, search for the highest in the file
        private $prefix='id';
        private $suffix='';
        private $mode='all';
        
        /* Ids will be of the form : 'prefixNBsuffix' where NB is a number
        mode : if 'all' : add ids to all tags; if 'leaves' only add ids to leaves. Default is 'all'.
        firstNumber is the first numeric value to use for the ids.
        */
        public function addIdsToXML($filename, $prefix = 'id', $suffix='' ,  $mode = 'all', $firstNumber=0){
            //$doc  = new DOMDocument();
            //$doc->load($filename);
            
            $this->prefix=$prefix;
            $this->suffix=$suffix;
            $this->mode=$mode;
            $this->id=$firstNumber;
            $root = new SimpleXMLElement($filename, NULL, TRUE);
            $this->addIdToElemAndChildren($root);
            
            $root->saveXML($filename);
            
        }   
            /*
            If tag doesn't already have one : add id.
            Then go recursively through the children.
            */
        private function addIdToElemAndChildren($elem){
            //add an id if all tags must have one, or this element is a leaf; and doesn't already have an id
            if(($this->mode=='all' || $elem->count() == 0) && !isset($elem['id'])){
                $elem->addAttribute('id', ''.$this->prefix.$this->id.$this->suffix);
                $this->id++;
            }
            foreach($elem->children() as $child){
                $this->addIdToElemAndChildren($child);
            }
        
        }
    
    }
    
?>