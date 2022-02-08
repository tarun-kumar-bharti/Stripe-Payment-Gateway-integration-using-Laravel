<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class SubscriptPayment Extends BaseModel {

    protected $table = 'subscription_payment';
    protected $fillable = array(
								'id',
								'subscriptionid',
								'userid',	
								'status'
						); 
	
	
	 static $STATUS_ARRAY = array(
									'A' => 'Active', 
									'D' => 'Deleted'	
							);
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 