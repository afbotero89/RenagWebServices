<?php

namespace App\v2\src\libs;

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

	public function writeResponse($res, $response){
		$res = $res->withHeader('Content-type', 'application/json');
		$res->getBody()
			->write(json_encode($response));

		return $res;
	}
}