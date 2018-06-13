<?php

namespace App\v2\src\libs;

class Message
{	
	private $errors = array(
		1 => "Invalid email format",
		2 => "Invalid status value",
		3 => "Invalid test status",
		4 => "Error updating record",
		5 => "Failed inserting record",
		6 => "Failed deleting record",
		7 => "Patient already exists",
		8 => "Email already exists",
		9 => "Missing data post"
		);

	private $success = array(
		1 => "Email ok",
		2 => "Value status ok",
		3 => "Test status ok",
		4 => "Successfully updated",
		5 => "Record inserted successfully",
		6 => "Record deleted successfully",
		7 => "Patient inserted successfully",
		8 => "Doctor inserted successfully",
		9 => "Data post OK"
		);

	public function getError($code){
		return $this->errors[$code];
	}

	public function getSuccess($code){
		return $this->success[$code];
	}
}
