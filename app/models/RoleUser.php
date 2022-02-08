<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Project
 *
 * @author tarun
 */

namespace App\models;


class RoleUser extends BaseModel {
    
    protected $table = 'role_user';
    
    protected $fillable = array(
                            "id",
                            "user_id",
                            "role_id",
                            "reporting_to",
                            "approver_id", 
                            "created_at", 
                            );
    
    
}