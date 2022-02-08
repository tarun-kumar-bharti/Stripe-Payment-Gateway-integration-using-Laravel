<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;;

class BaseController extends Controller {
    
    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    public function __construct(Request $request)//Dependency injection
    {
        $this->httpRequest = $request;
    }

}
