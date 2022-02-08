<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class Contact Extends BaseModel {

    protected $table = 'contact';
    protected $fillable = array(
								'id',
								'fname', 
								'lname',	
								'email',
								'subject', 
								'message',								 
								'status'
						);
 
	
	
	 static $STATUS_ARRAY = array(
									'A' => 'Active', 
									'D' => 'Deleted' 
							);
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 