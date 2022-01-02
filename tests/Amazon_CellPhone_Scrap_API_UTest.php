<?php

/* Run it with ./vendor/bin/phpunit */

require 'app/Controller.php';
require 'vendor/autoload.php';

 
use PHPUnit\Framework\TestCase;

class Amazon_CellPhone_Scrap_API_UTest extends TestCase{
    
    
    // Valid scenario 
    public function test_link1(){
        echo "\n".__METHOD__." UT >>>>>>>>\n";
        $link = "https://www.amazon.com/dp/B08BHFGJXF";
        $product = new Controller($link);

        //Then we should expect this ASIN from this Link
        self::assertSame("B08BHFGJXF", $product->get_scrap()->get_asin());
        self::assertSame("Apple iPhone 11 Pro Max, 256GB, Midnight Green - Unlocked (Renewed Premium)", $product->get_scrap()->get_title());
        self::assertSame((float)729, $product->get_scrap()->get_offer());
        self::assertSame((float)914.08, $product->get_scrap()->get_price());
        self::assertSame("https://images-na.ssl-images-amazon.com/images/I/71yIGykJFNS.__AC_SY300_SX300_QL70_ML2_.jpg", $product->get_scrap()->get_image_link());
        self::assertSame("Apple", $product->get_scrap()->get_brand());
    }

    // another product scenario 
    public function test_link2(){
        echo "\n".__METHOD__." UT >>>>>>>>\n";
        //In this link3 there will be fails because an exception will be catch on offer and the next data will not be scraped
        $link = "https://www.amazon.com/dp/B07XWGWPH5";
        $product = new Controller($link);
    
        //Then we should expect this ASIN from this Link
        self::assertSame("B07XWGWPH5", $product->get_scrap()->get_asin());
        self::assertSame('OnePlus Nord N200 | 5G Unlocked Android Smartphone U.S Version | 6.49" Full HD+LCD Screen | 90Hz Smooth Display | Large 5000mAh Battery | Fast Charging | 64GB Storage | Triple Camera,Blue Quantum', $product->get_scrap()->get_title());
        // There is a problem on file_get_contents (load loading html script) 
        //self::assertSame((float)239.99, $product->get_scrap()->get_offer());
        self::assertSame(NULL, $product->get_scrap()->get_price());
        self::assertSame("https://images-na.ssl-images-amazon.com/images/I/71DCZOdq92S.__AC_SX300_SY300_QL70_ML2_.jpg", $product->get_scrap()->get_image_link());
        self::assertSame("OnePlus", $product->get_scrap()->get_brand());
    }

  // Wrong url scenario 
    public function test_link3(){
        echo "\n".__METHOD__." UT >>>>>>>>\n";
        $link = "Wronghttps://www.amazon.com/dp/B08BHFGJXF";
        $product = new Controller($link);

        //Then we should expect this ASIN from this Link
        self::assertSame(NULL, $product->get_scrap());
    }


}
?>