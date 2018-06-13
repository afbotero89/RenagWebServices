<?php

namespace App\v1\src\libs;

class Validation
{
	private $response;
	
	function __construct()
	{
		$this->response = new Response();
	}

	public function verifyRequiredParams($required_fields, $request_fields) {
		$this->response = new Response();
	    $error = false;
	    $error_fields = "";

	    foreach ($required_fields as $field) {
	        if (!isset($request_fields[$field]) || strlen(trim($request_fields[$field])) <= 0) {
	            $error = true;
	            $error_fields .= $field . ', ';
	        }
	    }
	 
	    if ($error) {
	    	$this->response->setResponse(false, "Required field(s): [". substr($error_fields, 0, -2). "] are missing or empty");
	    	return $this->response;
	    }else{
	    	$this->response->setResponse(true);
	    	$this->response->result = true;
	    	return $this->response;
	    }
	}
}

?>