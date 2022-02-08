<?php 

namespace App\services;

use GuzzleHttp\Client; 
use Illuminate\Http\Request; 
 
use SoapClient; 
use stdClass;

class FedexService {  
     
    public function trackPackage($tracking){
       
        $responseObj = new stdClass();
        $responseObj->error = "";
		$responseObj->data  = [];
		
		$accessKey 	= getenv('FEDEX_ACCESS'); 
		$password	= getenv('FEDEX_PASS');
		$acctNum	= getenv('FEDEX_ACCNUM');
		$meterNum	= getenv('FEDEX_METER');

		$wsdlPath 	 = getenv('FEDEX_WSDL_PATH');;
		$servicepath = getenv('FEDEX_SERVICE_PATH');;
		
				 
		$soapClient = new SoapClient($wsdlPath, array('trace' => true));

		$request['SelectionDetails'] = array(
			'PackageIdentifier' => array(
				'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
				'Value' => $tracking  
			)
		);

		$request['WebAuthenticationDetail'] = array(
			'UserCredential'=> array(
				'Key' 		=> $accessKey, 
				'Password'	=> $password
			)
		);

		 
		$request['ClientDetail'] = array(
			'AccountNumber' => $acctNum, 
			'MeterNumber' 	=> $meterNum
		);

		 
		$request['TransactionDetail'] = array(
			'CustomerTransactionId' => 'Track Request via PHP'
		);
		
		 
		$request['Version'] = array(
			'ServiceId' 	=> 'trck', 
			'Major' 		=> 19, 
			'Intermediate'	=> 0, 
			'Minor' 		=> 0
		);
		 
		$request['ProcessingOptions'] = 'INCLUDE_DETAILED_SCANS';
 
		try {
			$shipment = $soapClient->track($request); 			
			 
			if($shipment->CompletedTrackDetails->TrackDetails->Notification->Code==0){ 
			 
			 $result =  $responseObj->data = $shipment->CompletedTrackDetails->TrackDetails->StatusDetail;  
			 
			 if($result){
				 $dataarray[0]['city'] 		= $result->Location->City;
				 $dataarray[0]['country'] 	= $result->Location->CountryName;
				 $dataarray[0]['status'] 	= $result->Description;			 					 
				 $dataarray[0]['datetime'] 	= substr($result->CreationTime,0,10);
			 }
			
			 $responseObj->data	 =  $dataarray;	
			
			}else{
				$error  = "No details available !"; 
				$responseObj->error = $error;
			} 
			 
		} catch (Exception $e) {
			$error  = "No details available !"; 
			$responseObj->error = $error;
		} 
        
        return $responseObj; 
        
    }   
} 