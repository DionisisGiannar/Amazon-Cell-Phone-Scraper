<?php
# Amazon Cell Phone Scrap API

require 'vendor/autoload.php';

class Product {
    private $host = '127.0.0.1';  
    private $user = 'DionGiannar';  
    private $pass = 'Password1!';  //Normally by configuration file in encrypted mode
    private $db = 'scrap_db1';
    private $link;  //the amazon url
    private $dom, $xpath; // DOM used by GuzzleHttp
    private $connected; // 0 for connected and 1 for not connectedin http url 
    private $asin="Not Found", /* Initialize Page vars */
            $title="Not Found", 
            $offer=NULL, 
            $price=NULL, 
            $review_stars="Not Found", 
            $ratings="Not Found", 
            $image_link="Not Found", 
            $brand="Not Found";
    
    //constructor
    function __construct($link) {
        $this->link = $link;
    }
    
    //getters and Setters
    public function get_asin(){return $this->asin;}
    public function get_title(){return $this->title;}
    public function get_offer(){return $this->offer;}
    public function get_price(){return $this->price;}
    public function get_review_stars(){return $this->review_stars;}
    public function get_ratings(){return $this->ratings;}
    public function get_image_link(){return $this->image_link;}
    public function get_brand(){return $this->brand;}

    public function connect_to_link(){ 
        try{
            //Connects on the webpage
            $httpClient = new \GuzzleHttp\Client();
            $response = $httpClient->get($this->link);
            $html = file_get_contents($this->link);
            //This line to suppress any warnings
            libxml_use_internal_errors(true);
            //Initialise all the Dom paths and Documents we will need 
            $this->dom = new DOMDocument();
            $this->dom->loadHTML($html);
            $this->xpath = new DOMXPath($this->dom);
            $this->connected = 0;
            return 0;
        } catch (Exception $e) {
            echo __METHOD__, " Caught exception: ",  $e->getMessage(), "\n";
            $this->connected = 1;
            return 1;
        }
    }

    // Function that finds the Asin, the title, the offer, the price, the stars, ratings and image link
    public function get_info(){
        
        try {
            if($this->connected == 1){
            return 1;
        }
            //Asin
            try{
                //Takes the elements of the table
                $elements =$this->xpath->query('//table[@id="productDetails_detailBullets_sections1"]');
                //If there are elements
                if(!is_null($elements)){
                    //Scan  the elements with th TagName till we find the value "ASIN" , in this position is the ASIN of the product
                    foreach($elements as $element){
                        $th = $element->getElementsByTagName('th');
                        for($n=0;$n<=$th->length-1;$n++){ 
                            if(trim($th->item($n)->textContent, " ") == "ASIN"){
                                break;
                            };
                        }
                        if($n != $th->length){
                            $this->asin = trim($element->getElementsByTagName('td')->item($n)->nodeValue, " ");
                        }
                    }
                }
                
            } catch(Exception $e) {
                echo __METHOD__. ' ASIN: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->asin = "Not Found";
            }
            //Title
            try{
                $this->title = trim($this->xpath->evaluate('//span[@id="productTitle"]')[0]->textContent, " ");
            } catch (Exception $e){
                echo __METHOD__. ' Title: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->title = "Not Found";
            }
            //Offer    /span[@class="a-price a-text-price a-size-medium apexPriceToPay"]
            try{
                $this->offer = (float)trim($this->xpath->evaluate('//span[@class="a-price a-text-price a-size-medium apexPriceToPay"]/span[@class="a-offscreen"]')[0]->textContent, "$");
            
            } catch(Exception $e) {
                echo __METHOD__. ' Offer: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->offer = NULL;
            }
            //Price
            try{
                $this->price = (float)trim($this->xpath->evaluate('//span[@class="a-price a-text-price a-size-base"]/span')[0]->textContent, "$");
            }  catch (Exception $e) {
                echo __METHOD__. ' Price: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->price = NULL;
            }
            //Review Stars
            try{
                $this->review_stars = trim($this->xpath->evaluate('//span[@class="a-icon-alt"]')[0]->textContent, " stars");
            
            } catch (Exception $e) {
                echo __METHOD__. ' Review Stars: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->review_stars = "Not Found";
            }
            //Ratings
            try{
                $this->ratings = trim($this->xpath->evaluate('//span[@id="acrCustomerReviewText"]')[0]->textContent, " ratings");
            } catch(Exception $e){
                echo __METHOD__. ' Ratings: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->ratings = "Not Found";
            }
            //Image Link
            try{
                $this->image_link = $this->xpath->evaluate('//div[@id="imgTagWrapperId"]/img')[0]->getAttribute('src');            
            } catch (Exception $e){
                echo __METHOD__. ' Image Link: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->image_link = "Not Found";
            }
            //Brand Name
            try{
                //Creates an array of all 2nd columns of the nodes
                foreach($this->dom->getElementsByTagName('td') as $ptag) {
                    if($ptag->getAttribute('class')=="a-span9") {
                        $td[] = $ptag->nodeValue; 
                    }
                }
                //Then takes the Brand from the 3rd potition
                $this->brand = trim($td[2], " ");
                unset ($td); //Delete the array (after u take wa=hat you want)
            }catch (Exception $e){
                echo __METHOD__. ' Brand: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->brand = "Not Found";
            }

        } finally {
            unset($this->dom);
            unset($this->xpath);
            return 0;
        }
    }

    // Function that saves data on the Database
    public function save_on_db(){
        if($this->connected == 1){ // Before the try, catch because we want to return and then go to finally cause the $conn can not be closed
            return 1;
        }
        try{
            
            // Create connection
            $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

            // Check connection
            if ($conn->connect_error) {
                die("db Connection failed: " . $conn->connect_error)."\n";
            } else {
                echo "db Connected successfully\n";
            }
            
            // Create Table if not exists. This is not efficient. Normally the table will be created outide the app.
            $sql = "CREATE TABLE IF NOT EXISTS Product(`ID` SERIAL PRIMARY KEY, product_id VARCHAR(20), title VARCHAR(200), offer FLOAT,
            price FLOAT, review_stars VARCHAR(100), ratings VARCHAR(30), image_link VARCHAR(500),
            brand VARCHAR(50))";  
            $conn->query($sql);
            
            // Insert data on the table (if product id already exist we could also update the row eg. by asin )
            $this->offer = !empty($this->offer) ? "'$this->offer'" : "NULL";
            $this->price = !empty($this->price) ? "'$this->price'" : "NULL";
            $sql = "INSERT INTO Product (product_id, title, offer, price, review_stars, ratings, image_link, brand)
            VALUES ('$this->asin', '$this->title', $this->offer, $this->price, '$this->review_stars', '$this->ratings', '$this->image_link', '$this->brand')";

            if ($conn->query($sql) == TRUE) {   
                //Print the Data for debugging reasons
                $sql = "SELECT * FROM Product ORDER BY ID DESC LIMIT 1"; // SELECT THE LAST ENTRY
                $result = $conn->query($sql);
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
            
            } else {
                echo __METHOD__. " Error: " . $sql . "\n" . $conn->error."\n";
            }

        } catch (Exception $e){
            echo __METHOD__, ' Caught exception: ',  $e->getMessage(), "\n";
        } finally {
            $conn->close(); 
            return 0;
        }
    }
}

############################################################
/*
echo "\n============a\n";
$the_site = "https://www.amazon.com/dp/B08BHFGJXF";
$product = new Product($the_site);
$product->connect_to_link(); // Put it on the constructor
$product->get_info();
$product->save_on_db();
*/
?>

