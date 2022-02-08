<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Plan Extends BaseModel {

    protected $table = 'mailboxplan';
    protected $fillable = array(
								'id',
								'planname', 
								'monthly_price',	
								'yearly_price', 		 
								'status'
						);
 
	
	
	 static $STATUS_ARRAY = array(
									'A' => 'Active', 
									'D' => 'Deleted' 
							);
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 