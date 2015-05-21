<?php

require 'haravan.php';
define("HARAVAN_API_KEY", "17920187d99a47bff0f5fe937d4c4383");
define("HARAVAN_SECRET", "d83c00c9f95923d9e3de91bc0c010238");
session_start();
    $sc = new HaravanClient($_SESSION['shop'], $_SESSION['token'], HARAVAN_API_KEY, HARAVAN_SECRET);

    try
    {
        // Get shop
        $shopjson = $sc->call('GET', '/admin/shop.json?page=1', array());
        
        echo "shop: " . $_SESSION['shop'];
        echo "<br/>token: " . $_SESSION['token'];
        echo "<br/>";
        
        echo "<pre>";
        var_dump($shopjson);
        echo "</pre>";
        

    }
    catch (HaravanApiException $e)
    {
        /* 
         $e->getMethod() -> http method (GET, POST, PUT, DELETE)
         $e->getPath() -> path of failing request
         $e->getResponseHeaders() -> actually response headers from failing request
         $e->getResponse() -> curl response object
         $e->getParams() -> optional data that may have been passed that caused the failure

        */
    }
    
?>