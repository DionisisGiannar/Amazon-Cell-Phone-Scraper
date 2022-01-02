<?php
// A class to use the table
require "Database.php";

class Product_Table {
    private $db; // the database instance
    private $product; // the product instance that we use 

    private $offer, $price;

    /* Constructor */ 
    function __construct($db){
        $this->db = $db;
        $this->create_if_not_exists();
    }

    /* Function that Creates a Product Table if not exists */
    public function create_if_not_exists(){
        $sql = "CREATE TABLE IF NOT EXISTS Product(`ID` SERIAL PRIMARY KEY, product_id VARCHAR(20), title VARCHAR(200), offer FLOAT,
        price FLOAT, review_stars VARCHAR(100), ratings VARCHAR(30), image_link VARCHAR(500),
        brand VARCHAR(50))";  
        $this->db->get_connector()->query($sql);
    }

    /* Function to insert data on the database Table */ 
    public function insert($product){
        $this->product = $product; // quick initialize the product
        try{
            $this->offer = !empty($product->get_offer()) ? $product->get_offer() : "NULL";
            $this->price = !empty($product->get_price()) ? $product->get_price() : "NULL";
            
            $sql = "INSERT INTO Product (product_id, title, offer, price, review_stars, ratings, image_link, brand)
                    VALUES ('".$product->get_asin()."', '".$product->get_title()."', $this->offer, $this->price, '".$product->get_review_stars()."',
                            '".$product->get_ratings()."', '".$product->get_image_link()."', '".$product->get_brand()."')";

            $this->db->get_connector()->query($sql);
        } catch (Exception $e){
                echo __METHOD__, ' Caught exception: ',  $e->getMessage(), "\n";
        }        

    }

    /* Function to print the last entry */ //For debuging purposes
    public function print_last_entry(){
        try{
            //Print the Data for debugging reasons
            $sql = "SELECT * FROM Product ORDER BY ID DESC LIMIT 1"; // SELECT THE LAST ENTRY
            $result = $this->db->get_connector()->query($sql);
            if ($result->num_rows > 0) {
                //Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "ID: ".$row["ID"]."\n";
                    echo "ASIN: ".$row["product_id"]."\n";
                    echo "Title: ".$row["title"]. "\n";
                    echo "Offer: ".$row["offer"]. "\n";
                    echo "Price: ".$row["price"]. "\n";
                    echo "Review Stars: ".$row["review_stars"]. "\n";
                    echo "Ratings: ".$row["ratings"]. "\n";
                    echo "Image_Link: ".$row["image_link"]. "\n";
                    echo "Brand: ".$row["brand"]. "\n";
                }
            } else {
                echo "Product table has no rows\n";
            }
        }
        catch (Exception $e){
            echo __METHOD__, ' Caught exception: ',  $e->getMessage(), "\n";
        }
    }
     




}

?>