<?php

namespace App\v1\src\libs;

class Response{

	public $result = null;
	public $response = false;
	public $message = 'Ha ocurrido un error inesperado';

	public function setResponse($response, $message = ''){
		// set params
		$this->response = $response;
		$this->message = $message;

		if(!$response && $message = '') $this->respomse = 'Ha ocurrido un error inesperado';
	}

	public function getResponse(){
		return $this->response;
	}

	public function getResult(){
		return $this->result;
	}
}