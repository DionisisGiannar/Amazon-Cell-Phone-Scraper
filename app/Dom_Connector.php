<?php
// Class for the connection of dom

require 'vendor/autoload.php';

class Dom_Connector {
    private $link, $dom, $xpath;//, $connected;

    /* Getters and Setters */
    public function get_dom(){return $this->dom;}
    public function get_xpath(){return $this->xpath;}

    /* Function that tells you if the connection is established */
    //public function is_connected(){return $this->connected;} // 0 for yes 1  for no
    
    /* Constructor */
    function __construct($link) { // sets the url that we use
        $this->link = $link;
    }
    /* Destructor */
    function __destruct(){
        //echo "Destroy\n";
        $this->dom = null;
        $this->xpath = null;
        unset($this->dom);
        unset($this->xpath);
        flush();
    }
       /* Function that connects on the url we want to scrap*/
    public function connect_to_link(){ 
        try{
            $html = file_get_contents($this->link);
            if($html == FALSE){
                throw new Exception('Wrong url');
            }
            //This line to suppress any warnings
            libxml_use_internal_errors(true);
            //Initialise all the Dom paths and Documents we will need 
            $this->dom = new DOMDocument();
            $this->dom->loadHTML($html); //there are problems inside loadHTML
            $this->xpath = new DOMXPath($this->dom);
            libxml_use_internal_errors(false);
        } catch (Exception $e) {
            echo __METHOD__, " Caught Warning: ",  $e->getMessage(), "\n";
            throw $e;
        }
    }
}
?>