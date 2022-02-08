<?php

namespace App\Http\Controllers;
use Auth;
use View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\models\User;  
use App\models\RoleUser;

use App\models\Plan;
use App\models\Subscription;
use App\models\SubscriptPayment;
use App\models\MailboxNumber;

use Exception;
use App\Payment;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
  

class PaymentsController  extends BaseController
{
    public $loggedinUsername;
    public $loggedinUserEmail;
    public $loggedinUserrole;
    public $loggedinUserrolearray;
    public $loggedinUserrolecode;
    public $loggedinUserid; 
    public $reporting_to;
	
	public function __construct() { 
       
        $user = Auth::user(); 
		if($user){
			$this->loggedinUserid     = $user->id;
			$this->loggedinUsername   = $user->name;
			$this->loggedinUserEmail  = $user->email;  
			$roles    = Auth::user()->roles;
		  
			
			$brancharray = array();
			$reportingarray = array();
			$approverarray = array();
			$membersUnderLoggeninUser = array();
			 
			 
			$this->loggedinUserrolearray = array();
			if(count($roles)>1){ 
				foreach($roles as $role){ 
					$this->loggedinUserrolearray[$role->name] = $role->display_name;
					$reportingarray[] = $role->pivot->reporting_to;
					$approverarray[]  = $role->pivot->approver_id; 
					 
				} 
			}else{ 
				$this->loggedinUserrolearray[$roles[0]->name]= $roles[0]->display_name;
				$reportingarray[] = $roles[0]->pivot->reporting_to;
				$approverarray[]  = $roles[0]->pivot->approver_id; 
			}
        
        
		   $this->loggedinUserrolecode = $roles[0]->name;
		   $this->loggedinUserrole     = $roles[0]->display_name; 
		   $this->reporting_to         = $reportingarray;
		   $this->approver_id          = $approverarray; 
		}
 
       View::share ( 'loggedinUsername',  $this->loggedinUsername);
       View::share ( 'loggedinUserEmail', $this->loggedinUserEmail); 
       View::share ( 'loggedinUserrole',  $this->loggedinUserrole);
       View::share ( 'loggedinUserrolearray',  $this->loggedinUserrolearray);
       View::share ( 'loggedinUserrolecode',  $this->loggedinUserrolecode);
       
      
       View::share ( 'reporting_to',  $this->reporting_to);
  
    }  
			
     
    /**
     * Show instajobs home.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 
     public function paymentCheckout(Request $request){
		 
		$secret_key = getenv('STRIPE_SECRET_KEY');		
		\Stripe\Stripe::setApiKey($secret_key);	 
		
		$YOUR_DOMAIN = getenv('APP_URL');
		
		if(Auth::user()) { 		
			$user = Auth::user();	
			$subscpObj = Subscription::where('userid','=',$user->id)->first();			  
			if(count((array) $subscpObj)>0){
			   if($subscpObj->plan_status=='P'){
				   $planobj = Plan::find($subscpObj->planid);
				   
				   if($subscpObj->plan_duration==12){
						$price 		= $planobj->yearly_price; 
					}else if($subscpObj->plan_duration==1){
						$price 		= $planobj->monthly_price; 
					}else{
						$price 		=  $planobj->monthly_price*$subscpObj->plan_duration; 
					}
				    
				   //////////////////////////////////////////////////
					
					$checkout_session = \Stripe\Checkout\Session::create([
					  'payment_method_types' => ['card'],
					  'line_items' => [[
						'price_data' => [
						  'currency' => 'usd',
						  'unit_amount' => $price,
						  'product_data' => [
							'name' => $subscpObj->planname,
							'images' => [],
						  ],
						],
						'quantity' => 1,
					  ]],
					  'mode' => 'payment',
					  'success_url' => $YOUR_DOMAIN . '/payment-thank-you',
					  'cancel_url' => $YOUR_DOMAIN . '/payment-cancel',
					]);
					
				return Response()->json(array(
							'success'   => true ,
							'id'		=> $checkout_session->id				 
				)); 
					 
				 /////////////////////////////////////////////////
			   }
			}
			
		} 
		 
    }

    public function paymentThank(){
			
		if(Auth::user()) { 		
			$user = Auth::user();	
			$subscpObj = Subscription::where('userid','=',$user->id)->first();		
 
			if(count( (array)$subscpObj)>0){ 
			
			   if($subscpObj->plan_status=='P'){
				   $subscpObj->plan_status 		= 'A';
				   $subscpObj->plan_start_date 	= date('Y-m-d'); 
				   $subscpObj->plan_expire_date = date("Y-m-d", strtotime(date('Y-m-d')." +".$subscpObj->plan_duration." Month"));
				   $subscpObj->status 			= 'A';
				   $subscpObj->update(['id' => $subscpObj->id]);
				   
				   
				   $getmailboxobj = MailboxNumber::find($subscpObj->mailboxid);
				   $getmailboxobj->status = 'U';
				   $getmailboxobj->update(['id' => $getmailboxobj->id]);
				   
				   
				   
				   
				   //////////////////////////////////////////////////
						 
					$subject = "Physical Mailbox Subscription" ; 
					$sentby  = getenv('ADMIN_EMAIL');
					$toemail = $user->email;
					$toname  = $user->name; 
					 
					$data = array(    
									'sentby'                => $sentby,
									'toemail'               => $toemail,
									'toname'                => $toname,
									'subject'               => $subject,
									'adminemail'            => getenv('ADMIN_EMAIL'),
									'adminname'             => getenv('ADMIN_NAME'),
									'subscpObj'               => $subscpObj, 
							); 
				 
					Mail::send( 'emails.mailboxpurchase_mail', $data, function( $message ) use ($data)
					{   
						 
						$message->to( $data['toemail'] , $data['toname'] ); 
									
						$message->from( $data['adminemail'], $data['adminname'])
								->subject($data['subject']); 
						
					}); 
					
					
					Mail::send( 'emails.mailboxtoadmin_mail', $data, function( $message ) use ($data)
					{   
						 
						$message->to( $data['adminemail'] , $data['adminname'] ); 
									
						$message->from( $data['adminemail'], $data['adminname'])
								->subject($data['subject']); 
						
					}); 
					 
					////////////////////////////////////////////////////////////// 
				    
				   
				   $planobj = Plan::find($subscpObj->planid);
				   
				   return view('user.paymentthankyou')
							   ->with('pageflag',3)
							   ->with('subscpObj',$subscpObj)
							   ->with('planobj',$planobj);
			   
				}else{
				   return Redirect::to('/user-dashboard');
				}
			}
		}				
    }
	
	 public function paymentCancel(){
		if(Auth::user()) { 		
			$user = Auth::user();	
			$subscpObj = Subscription::where('userid','=',$user->id)->first();			  
			if(count((array) $subscpObj)>0){
			   if($subscpObj->plan_status=='P'){ 
					
				   if($subscpObj->mailboxid>0){		
					   $getmailboxobj = MailboxNumber::find($subscpObj->mailboxid);
					   $getmailboxobj->status = 'A';
					   $getmailboxobj->usedby = 0;
					   $getmailboxobj->update(['id' => $subscpObj->mailboxid]);
					   
					   $subscpObj->mailboxid	= 0;
					   $subscpObj->mailboxnumber= '';
					   $subscpObj->update(['id' => $subscpObj->id]);				   
				   }
								   
				
				   $planobj = Plan::find($subscpObj->planid);
				   return view('user.paymentcancel')
							   ->with('pageflag',3)
							   ->with('subscpObj',$subscpObj)
							   ->with('planobj',$planobj);
			   }
			}
		}		
        
    }
	
} 
 