<?php

namespace App\v2\src\libs;

class Validation
{
	private $response;
	private $message;
	
	function __construct()
	{
		$this->response = new Response();
		$this->message = new Message();
	}

	// Validate fields required
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

	//validate format email
	public function validateEmail($email){
		$this->response = new Response();
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->response->setResponse(true);
			$this->response->result = $this->message->getSuccess(001);
		}else{
			$this->response->setResponse(false, $this->message->getError(001));
		}

		return $this->response;
	}

	//Validate content field
	public function validateSpecificValue($value, $options){
		$this->response = new Response();

		if(in_array($value, $options)){
			$this->response->setResponse(true);
			$this->response->result = $this->message->getSuccess(002);
		}else{
			$this->response->setResponse(false, $this->message->getError(002));
		}

		return $this->response;
	}
}

?>