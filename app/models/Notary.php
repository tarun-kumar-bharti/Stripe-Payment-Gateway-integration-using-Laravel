<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Notary Extends BaseModel {

    protected $table = 'notary';
    protected $fillable = array(
								'id',
								'fname', 
								'lname',	
								'email',
								'contact', 
								'slottiming',
								'notaryservice',
								'preferences',
								'status'
						);
 
	
	
	 static $STATUS_ARRAY = array(
									'A' => 'Active', 
									'R' => 'Rejected',
									'D'	=> 'Deleted'
							);
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 