<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Subscription Extends BaseModel {

    protected $table = 'subscription';
    protected $fillable = array(
								'id',
								'userid', 
								'planid',	
								'planname', 		 
								'plan_start_date',
								'plan_status',
								'name',								 
								'email',
								'contact',
								'company',
								'country',
								'address',
								'city',
								'state',
								'pincode',
								'mailboxid',
								'mailboxnumber',
								'status' 
						);
 
	 static $STATUS_ARRAY = array(
									'A' => 'Active', 
									'E' => 'Expire',
									'P' => 'Pending'
								);
	 
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 