<?php

namespace App\Http\Controllers\Auth;

use View;
use Auth;

use App\models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request; 
use App\services\EmailService;
use stdClass;
 
use Illuminate\Http\Response;
 
class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
           
    
    public $loggedinUsername;
    public $loggedinUserEmail;
    public $loggedinUserrole;
    public $loggedinUserrolearray;
    public $loggedinUserrolecode;
    public $loggedinUserid;
    public $userbranchid;
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

	public function changePasswordfromDashboard(Request $request) {  
		 
		if($this->loggedinUserrolecode!='USER'){
			return view('admin.adminchangepass')->with('activepage','');  
		}else{
			//return view('changepass')->with('activepage','');  
		} 
	}
	  
	  
	public function savePasswordfromDashboard(Request $request) {
        $user = Auth::user();  
        $user->id;
        $user->name;
        $user->email; 
        
        $formData= $request->all();  
        
        $responseObj    = new stdClass(); 
        $responseObj->saveflag = 0;        
        $responseObj->msg = '';  
        
        if($formData['oldpassword']==''){
            $responseObj->saveflag=1;
            $responseObj->msg     = "Please enter old password !"; 
        }else if($formData['newpassword']==''){
            $responseObj->saveflag=1;
            $responseObj->msg     = "Please enter new password !"; 
        }else if(strlen($formData['newpassword'])<6){
            $responseObj->saveflag=1;
            $responseObj->msg     = "New password should be more then five characters long !"; 
        }else if($formData['cnfpassword']==''){
            $responseObj->saveflag=1;
            $responseObj->msg     = "Please enter confirm password !"; 
        }else if($formData['newpassword']!=$formData['cnfpassword']){
            $responseObj->saveflag=1;
            $responseObj->msg     = "New password and confirm password are not same !"; 
        } 
       
        if($responseObj->saveflag==0){
                
			if (Auth::attempt(['email' => $user->email, 'password' => $formData['oldpassword'], 'status' => 'A'])) {
				
				User::where('email','=',$user->email)->update( ['password'=>bcrypt($formData['newpassword'])]);
				  
				$flag = true; 
				$responseObj->msg = 'Password updated successfully !';  	

			}else{
				$flag = false; 
				$responseObj->saveflag=1;
				$responseObj->msg     = "Old password does not match !"; 
			}
                
        }else{
                $flag = false; 
        }
        
        return Response()->json(array(
                    'success'   => $flag, 
                    'msg'       => $responseObj->msg
        ));
    }
	
     
}
