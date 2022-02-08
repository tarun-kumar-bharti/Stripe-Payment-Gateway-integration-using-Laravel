<?php 

namespace App\services;

use GuzzleHttp\Client; 
use Illuminate\Http\Request; 
 
use stdClass;

class UpsService {  
     
    public function trackPackage($tracking){
        
		$responseObj = new stdClass(); 
		$responseObj->error = "";
		$responseObj->data  = [];
		
		$header = array(
			"content-type: application/json",
			"Accept: application/json",
			"AccessLicenseNumber:".getenv('UPS_ALN'),
			"Username:".getenv('UPS_USER'),
			"Password:".getenv('UPS_PASS'),
			"transactionSrc:".getenv('UPS_TXN') 
		);  
		 
		$url = getenv("UPS_API_URL").$tracking."?local=en_US"; 
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3600); 
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		$result = curl_exec($curl);			 
		$dataarray = array();
		$error		= "";
		if($result){ 
			$res = json_decode($result);
			if(isset($res->trackResponse)){
				 
				foreach($res->trackResponse->shipment[0]->package[0]->activity as $key=>$value){
					
					$dataarray[$key]['city'] 	= $value->location->address->city;
					$dataarray[$key]['country'] = $value->location->address->country;
					$dataarray[$key]['status'] 	= $value->status->description;						
					$arr = str_split($value->date, 4);						
					$dataarray[$key]['date'] = $arr[0]."-".substr(chunk_split($arr[1], 2, '-'), 0, -1);						 
					$dataarray[$key]['time'] 	= substr(chunk_split($value->time, 2, ':'), 0, -1);  
				} 
				
				$responseObj->data = $dataarray;
				
			}else{
			 
				if(count($res->response->errors)>0){					
					$error  = "No details available !";//$res->response->errors[0]->message;	
					$responseObj->error = $error;	
				}
			}  
			 
		}else{
			 curl_error($curl);
		} 
		curl_close($curl); 
	
		return $responseObj; 
        
    }   
} 