<?php 

namespace App\services;

use GuzzleHttp\Client; 
use Illuminate\Http\Request; 
 
use SoapClient; 
use stdClass;

class UspsService {  
     
    public function trackPackage($tracking){
       
        $responseObj = new stdClass();
        $responseObj->error = "";
		$responseObj->data  = []; 
        
        return $responseObj; 
        
    }   
} 