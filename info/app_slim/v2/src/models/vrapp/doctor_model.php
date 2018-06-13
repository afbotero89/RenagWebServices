<?php

namespace App\v2\src\models\vrapp;

use App\v2\src\libs\Message;
use App\v2\src\libs\Response;

class Vrapp_doctor_model extends Vrapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
		$this->message = new Message();
	}

		//construct and compute a get query
	private function constructGet($query, $data = ""){
		$m_response = new Response();
		try{
			$stmt = $this->db->prepare($query . " status = 1 AND api_version = " . $this->api_version);

			if($data != ""){
				$stmt->execute($data);
			}else{
				$stmt->execute();
			}

			$m_response->setResponse(true);
			$m_response->result = $stmt->fetchAll();
		}catch(Exception $e){
			$m_response->setResponse(false, $e->getMessage());
		}

		return $m_response;
	}

	//get all doctors
	public function getAll(){
		$query = "SELECT * FROM " . $this->tables['doctors'] . " WHERE";

		return $this->constructGet($query);
	}

	// get doctor login
	public function getByLogin($email, $pass){
		$query = "SELECT * FROM " . $this->tables['doctors'] . " WHERE email = $email AND password = $pass AND";

		return $this->constructGet($query);
	}

	// get doctor by email
	public function getByEmail($email){

		$query = "SELECT * FROM " . $this->tables['doctors'] . " WHERE email = ? AND";

		$data = array(
			$email
			);

		return $this->constructGet($query, $data);
	}

	//Insert or update a doctor
	public function insertOrUpdate($data){
		try{
			if(isset($data['id'])){// update

				$query = "UPDATE " . $this->tables['doctors'] . " SET
						name = ?,
						lastname = ?,
						WHERE id = ?";

				$udata = array(
					$data['name'],
					$data['lastname'],
					$data['id']
					);

				$stmt = $this->db->prepare($query);
				$stmt->excecute($udata);
				$affected = $stmt->rowCount();

				if($affected > 0 ){
					$this->response->setResponse(true, $this->message->getSuccess(4));
					$this->response->result = array("success" => "1");
				}else{
					$this->response->setResponse(false, $this->getError(4));
				}

			}else{ //Insert

				//Verify if exist email
				$record = $this->getByEmail($data['email']);

				if($record->response){

					if(!count($record->result) > 0){

						$query = "INSERT INTO " . $this->tables['doctors'] . " (name, lastname, email, password, api_version) VALUES (?,?,?,?," . $this->api_version . ")";

						$idata = array(
							$data['name'],
							$data['lastname'],
							$data['email'],
							$data['password']
							);

						$stmt = $this->db->prepare($query);
						$stmt->execute($idata);
						$id = $this->db->lastInsertId();
						$affected = $stmt->rowCount();

						if($affected > 0 ){
							$this->response->setResponse(true, $this->message->getSuccess(5));
							$this->response->result = $id;
						}else{
							$this->response->setResponse(false, $this->message->getError(5));
						}

					}else{
						$this->response->setResponse(false, $this->message->getError(8));
					}
				}else{
					$this->response->setResponse(false, $this->message->getError(10));
				}
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	// delete a doctor
	public function delete($id){
		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tables['doctors'] . " SET status = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			if($affected > 0 ){
				$this->response->setResponse(true, $this->message->getSuccess(6));
				$this->response->result = array('success' => '1');
			}else{
				$this->response->setResponse(false, $this->message->getError(6));
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}
}