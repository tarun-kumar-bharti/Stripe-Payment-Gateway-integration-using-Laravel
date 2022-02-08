<?php 

namespace App\services;

use GuzzleHttp\Client; 
use Illuminate\Http\Request; 
 
use SoapClient; 
use stdClass;

class DhlService {  
     
    public function trackPackage($tracking){
       
        $responseObj = new stdClass();
        $responseObj->error = "";
		$responseObj->data  = [];
		 
		$header = array(
			"content-type: application/json",
			"Accept: application/json",
			"DHL-API-Key:".getenv('DHL_API_KEY') 
		); 
					

		$url = getenv('DHL_API_URL').'='.$tracking;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);		 
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3600);		 
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		$result = curl_exec($curl);
		 
		echo "<pre>";
		if($result){ 
			print_r(json_decode($result));
		}else{
			curl_error($curl);
		}	

		curl_close($curl);
        
        //return $responseObj; 
        
    }   
} 