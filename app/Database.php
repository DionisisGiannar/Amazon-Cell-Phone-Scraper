<?php
// Class for the database connection

require 'vendor/autoload.php';

class Database {
    private $host;// = '127.0.0.1';  
    private $user;// = 'DionGiannar';  
    private $pass;// = 'Password1!';  //Normally by configuration file in encrypted mode
    private $db;// = 'scrap_db1';
    private $conn; // connector

    /* Getters and Setters */
    //public function set_connector($conn){$this->conn = $conn;}
    public function get_connector(){return $this->conn;}
    
    function __construct($host, $user, $pass, $db){
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db = $db;
        $this->connect();
    } 

    /* Function that Connecta to the database */
    public function connect(){
        try {
            // Create connection
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

            // Check connection
            if ($this->conn->connect_error) {
                die("db Connection failed: " . $this->conn->connect_error)."\n";
            } else {
                echo "db Connected successfully\n";
            }
        } catch (Exception $e){
            echo __METHOD__, ' Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}

?>