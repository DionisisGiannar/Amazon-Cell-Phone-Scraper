<?php

require "Amazon_Product_Scrap.php";
require "Product_Table.php";

class Controller{
    private $scrap;
    private $db_conn, $db_table;

    public function get_scrap(){return $this->scrap;}
    public function get_db_connection(){return $this->db_conn;}
    public function get_db_table(){return $this->db_table;}

    function __construct($link){
        echo "INITIALIZE\n";
        try{
            $this->scrap = new Amazon_Product_Scrap($link);
            $this->scrap->get_info();

            $this->db_conn = new Database('127.0.0.1', 'DionGiannar', 'Password1!', 'scrap_db1');
            $this->db_table = new Product_Table($this->db_conn);
            $this->db_table->insert($this->scrap);
            $this->db_table->print_last_entry();
        } catch (Exception $e){
            echo __METHOD__, " Caught Warning: ",  $e->getMessage(), "\n";
        }
        
        
        
    }

}

/*echo "START\n";
$product = new Controller("https://www.amazon.com/dp/B08BHFGJXF");
//unset($product);

$product1 = new Controller("sfsfhttps://www.amazon.com/dp/B07XWGWPH5");
//unset($product1);
*/
?>
