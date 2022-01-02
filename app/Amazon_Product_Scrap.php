<?php
// A class for scrapping information
require "Dom_Connector.php";

class Amazon_Product_Scrap {
    
    private $asin="Not Found", $title="Not Found", $offer=NULL, $price=NULL, /* Initialize Page vars */
            $review_stars="Not Found", $ratings="Not Found", $image_link="Not Found", $brand="Not Found";
    private $dom_conn;
    //getters and Setters
    public function get_asin(){return $this->asin;}
    public function get_title(){return $this->title;}
    public function get_offer(){return $this->offer;}
    public function get_price(){return $this->price;}
    public function get_review_stars(){return $this->review_stars;}
    public function get_ratings(){return $this->ratings;}
    public function get_image_link(){return $this->image_link;}
    public function get_brand(){return $this->brand;}

    //public function get_dom_conn(){return $this->dom_conn;}

    function __construct($link){
        try{
            $this->dom_conn = new Dom_Connector($link);
            $this->dom_conn->connect_to_link();
        } catch (Exception $e) {
            echo __METHOD__, " Caught Warning: ",  $e->getMessage(), "\n";
            throw $e;
        }
    }

    function __destruct() {
        $this->dom_conn = null; 
        unset($this->dom_conn);
        flush();
    }

    // Function that finds the Asin, the title, the offer, the price, the stars, ratings and image link
    public function get_info(){
        
        try {
            //Asin
            try{
                //Takes the elements of the table
                $elements =$this->dom_conn->get_xpath()->query('//table[@id="productDetails_detailBullets_sections1"]');
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
                $this->title = trim($this->dom_conn->get_xpath()->evaluate('//span[@id="productTitle"]')[0]->textContent, " ");
            } catch (Exception $e){
                echo __METHOD__. ' Title: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->title = "Not Found";
            }
            //Offer    /span[@class="a-price a-text-price a-size-medium apexPriceToPay"]
            try{
                $this->offer = (float)trim($this->dom_conn->get_xpath()->evaluate('//span[@class="a-price a-text-price a-size-medium apexPriceToPay"]/span[@class="a-offscreen"]')[0]->textContent, "$");
            
            } catch(Exception $e) {
                echo __METHOD__. ' Offer: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->offer = NULL;
            }
            //Price
            try{
               $this->price = (float)trim($this->dom_conn->get_xpath()->evaluate('//span[@class="a-price a-text-price a-size-base"]/span')[0]->textContent, "$");
            }  catch (Exception $e) {
                echo __METHOD__. ' Price: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->price = NULL;
            }
            //Review Stars
            try{
                $this->review_stars = trim($this->dom_conn->get_xpath()->evaluate('//span[@class="a-icon-alt"]')[0]->textContent, " stars");
            
            } catch (Exception $e) {
                echo __METHOD__. ' Review Stars: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->review_stars = "Not Found";
            }
            //Ratings
            try{
                $this->ratings = trim($this->dom_conn->get_xpath()->evaluate('//span[@id="acrCustomerReviewText"]')[0]->textContent, " ratings");
            } catch(Exception $e){
                echo __METHOD__. ' Ratings: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->ratings = "Not Found";
            }
            //Image Link
            try{
                $this->image_link = $this->dom_conn->get_xpath()->evaluate('//div[@id="imgTagWrapperId"]/img')[0]->getAttribute('src');            
            } catch (Exception $e){
                echo __METHOD__. ' Image Link: Caught exception: '.  $e->getMessage(), "\n"; 
                $this->image_link = "Not Found";
            }
            //Brand Name
            try{
                //Creates an array of all 2nd columns of the nodes
                foreach($this->dom_conn->get_dom()->getElementsByTagName('td') as $ptag) {
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
            echo "finished scrap\n";
            //unset($this->dom_conn);  
        }
    }
}




?>
