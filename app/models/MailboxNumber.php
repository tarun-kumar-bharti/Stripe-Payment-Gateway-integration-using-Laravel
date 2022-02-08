<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;

class MailboxNumber Extends BaseModel {

    protected $table = 'mailboxnumber';
    protected $fillable = array(
								'id',
								'mailboxnumber', 
								'usedby',
								'status'
						); 
	
	
	 static $STATUS_ARRAY = array(
									'A' => 'Available', 
									'U' => 'Used',
									'B' => 'Block'	
							);
    const STATUS_ACTIVE         = 'A'; //added status
    const STATUS_INACTIVE       = 'D'; //deleted status
	 
}
 