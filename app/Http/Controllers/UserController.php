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
use stdClass;
use Session;

class UserController  extends BaseController
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
	 
	 public function showDashboard(){		 
		if(Auth::user()) { 		
			$user = Auth::user(); 
			
			$status = Subscription::$STATUS_ARRAY;		
		 
			$subscpObj = Subscription::where('userid','=',$user->id)->where('plan_status','!=','P')->get();
			return view('user.dashboard')->with('pageflag',6)->with('subscpObj',$subscpObj)->with('status',$status); 
		} 			
    } 
	
	
	public function changePassword(){		 
		if(Auth::user()) { 		
			$user = Auth::user();  
			return view('user.changepassword')->with('pageflag',7); 
		} 			
    } 
	
	
	
	public function getPlanprice(Request $request){ 
		 $flag = false;  	
		 $formData= $request->all(); 
		 if($formData['duration']>0 && $formData['duration']<=12){
			$planobj = Plan::find($formData['pid']);
			if($planobj->id>0){	
				 
				if($formData['duration']==12){					 
					$price 		= $planobj->yearly_price;
					$duration   =  "1 Year";
				}else if($formData['duration']==1){
					$price 		= $planobj->monthly_price;
					$duration   =  "1 month";
				}else{
					$price 		=  $planobj->monthly_price*$formData['duration'];
					$duration   =  $formData['duration']." months";
				}
				 $flag = true;  	
				 return Response()->json(array(
						'success'   => $flag ,
						'price'		=> "$ ".$price,
						'mplan'		=> 'Your Plan Summary: '.$planobj->planname,
						'duration'	=> $duration	
				));  
			}
		 }		 
	}
	
	public function subScribeMailbox($id){ 
		if(Auth::user()) { 		
			$user = Auth::user();
			
			$subscpObj = Subscription::where('userid','=',$user->id)->first();
			if(count((array) $subscpObj)>0){ 
				
				if($subscpObj->plan_status=='P'){ 
					$planall = Plan::where('status','=','A')->get();	
					$planobj = Plan::find($subscpObj->planid);			
					return view('user.physicalmailboxform')
							->with('plan',$planobj)
							->with('allplan',$planall)
							->with('pageflag',3)							
							->with('subscpObj',$subscpObj);
							
					
				}else if($subscpObj->plan_status=='A'){				
					return Redirect::to('/user-dashboard');
				}				
			
			}else{			
				if($id!=''){
					$planall = Plan::where('status','=','A')->get();	
					$planobj = Plan::find($id);			
					return view('user.physicalmailboxform')
							->with('plan',$planobj)
							->with('allplan',$planall)
							->with('pageflag',3)						
							->with('subscpObj',[]);
				} 	
			}
		}else{
			return Redirect::to('/register');		
		}			
    } 
	
	public function submitSubscription(Request $request){ 
		 $flag = false;  	
		 $formData= $request->all(); 
		 
		 if(Auth::user()) { 		
			 $user = Auth::user(); 
			 
			 $subscpObj = Subscription::where('userid','=',$user->id)->first();
		 
			 if(count((array) $subscpObj)>0){
				   if($subscpObj->plan_status=='P'){
					 
				   }else if($subscpObj->plan_status=='A'){					 
					 $flag = true;  	
					 return Response()->json(array(
							'success'   => $flag ,
							'url'		=> url('user-dashboard')
							 
					));					
				  }	
				  $new = 'N';
				
			 }else{	
				$subscpObj = new Subscription();	
				$new = 'Y';	
			 } 
			   
			 $subscpObj->plan_status 		= 'P';
			 $subscpObj->status 			= 'P';
		 
			 $subscpObj->planid 		= $formData['planid'];
			 $subscpObj->planname 		= $formData['mailboxplan'];
			 $subscpObj->plan_duration 	= $formData['duration'];
			 			 	 
			 
			 $subscpObj->userid  = $user->id;
			 $subscpObj->name    = $user->name;
			 $subscpObj->email   = $user->email;
			 $subscpObj->phone 	 = $user->contact_number;
			 
			 $subscpObj->company = $formData['company'];
			 $subscpObj->country = $formData['country'];
			 $subscpObj->address = $formData['address'];
			 $subscpObj->city 	 = $formData['city'];
			 $subscpObj->state 	 = $formData['state'];
			 $subscpObj->pincode = $formData['pincode'];
			 
			 $subscpObj->mailboxid 		= '';
			 $subscpObj->mailboxnumber  = ''; 
				  
			 if($new=='N'){				
				$subscpObj->update(['id' => $subscpObj->id]);				
			 }else{
				$subscpObj->save();
			 } 
			 
			 if($subscpObj->id>0){
				 $flag = true;  	
				 return Response()->json(array(
						'success'   => $flag ,
						'url'		=> url('pay-for-physical-mailbox')
						 
				)); 
				 
			 }
		 
		 } 
		 
	} 
	
	public function payMailbox(){	
		if(Auth::user()) { 	
			$msg = "";
			$user = Auth::user();		
			$subscpObj = Subscription::where('userid','=',$user->id)->first();					 
			if(count((array) $subscpObj)>0){
			   if($subscpObj->plan_status=='P'){
				   
				   if($subscpObj->mailboxid!=0){					   
					   $getmailboxobj = MailboxNumber::find($subscpObj->mailboxid);
					   if(count((array) $getmailboxobj)>0){
						   if($getmailboxobj->status=='B' && $getmailboxobj->usedby==$user->id){
							   //do nothing
						   }else{							   
							   $getavailablemailbox = MailboxNumber::where('status','=','A')->first();
							   if(count((array) $getavailablemailbox)>0){
								   $getavailablemailbox->status='B';
								   $getavailablemailbox->usedby=$user->id;
								   $getavailablemailbox->update(['id' => $getavailablemailbox->id]);	
								   
								   $subscpObj->mailboxid	= $getavailablemailbox->id;
								   $subscpObj->mailboxnumber= $getavailablemailbox->mailboxnumber;
								   $subscpObj->update(['id' => $subscpObj->id]);
							   }else{
								   $subscpObj->mailboxid	= 0;
								   $subscpObj->mailboxnumber= '';
								   $subscpObj->update(['id' => $subscpObj->id]);
								   $msg = "No mailbox number available !";
							   }
						   }
					   }
					    
					   
				   }else{
					   $getavailablemailbox = MailboxNumber::where('status','=','A')->first();
					   if(count( (array) $getavailablemailbox)>0){
						   $getavailablemailbox->status='B';
						   $getavailablemailbox->usedby=$user->id;
						   $getavailablemailbox->update(['id' => $getavailablemailbox->id]);	
						   
						   $subscpObj->mailboxid	= $getavailablemailbox->id;
						   $subscpObj->mailboxnumber= $getavailablemailbox->mailboxnumber;
						   $subscpObj->update(['id' => $subscpObj->id]);
					   }else{
						   $subscpObj->mailboxid	= 0;
						   $subscpObj->mailboxnumber= '';
						   $subscpObj->update(['id' => $subscpObj->id]);
						   $msg = "No mailbox number available !";
					   }
				   }
				   
				   
				   $planobj = Plan::find($subscpObj->planid);					
				   return view('user.paymailbox')
							   ->with('pageflag',3)
							   ->with('subscpObj',$subscpObj)
							   ->with('planobj',$planobj)
							   ->with('msg',$msg);	 			
			   }else if($subscpObj->plan_status=='A'){
				   return Redirect::to('/user-dashboard');
			   }
			} 
		}
		
    }  
	
} 