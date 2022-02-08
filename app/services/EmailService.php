<?php 

namespace App\services;
use Mail;
use GuzzleHttp\Client; 
use Illuminate\Http\Request; 
 
use DB; 
use stdClass;


class EmailService {  
     
    public function sendMailForPasswordReset($userObj,$url){
        
        $responseObj = new stdClass();
        $responseObj->msg        = "Failed to send mail !"; 
        $responseObj->saveflag   = 1; 
        
        $subject = "Password Reset Mail" ; 
        $sentby  = getenv('ADMIN_NAME');
        $toemail = $userObj->email;
        $toname  = $userObj->name;
        
        
        $data = array(    
                        'sentby'                => $sentby,
                        'toemail'               => $toemail,
                        'toname'                => $toname,
                        'subject'               => $subject,
                        'adminemail'            => getenv('ADMIN_EMAIL'),
                        'adminname'             => getenv('ADMIN_NAME'), 
                        'userObj'               => $userObj,
                        'url'                   => $url
                    );
      
        
        Mail::send( 'emails.password_reset_mail', $data, function( $message ) use ($data,$userObj,$responseObj)
        {   
             
            $message->to( $userObj->email , $userObj->name ); 
                        
            $message->from( $data['adminemail'], $data['adminname'])
                    ->subject($data['subject']); 
            
            $responseObj->msg        = "Main sent successfully !"; 
            $responseObj->saveflag   = 0;
        }); 
        
        return $responseObj; 
        
    } 
     
    
} 