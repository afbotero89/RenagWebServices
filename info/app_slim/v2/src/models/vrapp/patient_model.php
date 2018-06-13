<?php

namespace App\v2\src\models\vrapp;

use App\v2\src\libs\Message;
use App\v2\src\libs\Response;

class Vrapp_patient_model extends Vrapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
		$this->message = new Message();
	}

	//construct and compute a get query
	private function constructGet($query){
		try{
			$stmt = $this->db->prepare($query . " status = 1 AND api_version = " . $this->api_version);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	//get all patients
	public function getAll(){
		$query = "SELECT * FROM " . $this->tables['patients'] . " WHERE";

		return $this->constructGet($query);
	}

	//get all patients by doctor
	public function getAllByDoctor($doctor_id){
		$query = "SELECT * FROM " . $this->tables['patients'] . " WHERE doctor_id = $doctor_id AND";

		return $this->constructGet($query);
	}

	//get active by doctor
	public function getActiveByDoctor($doctor_id){
		$query = "SELECT * FROM " . $this->tables['patients'] . " WHERE doctor_id = $doctor_id AND";

		return $this->constructGet($query);
	}

	// get patient by document
	public function getByDocument($document){
		$query = "SELECT * FROM " . $this->tables['patients'] . " WHERE document = $document AND";

		return $this->constructGet($query);
	}

	// get patient by id
	public function getById($id){
		$query = "SELECT * FROM " . $this->tables['patients'] . " WHERE id = $id AND";

		return $this->constructGet($query);
	}

	// Insert or update a patient
	public function insertOrUpdate($data){
		try{
			if(isset($data['id'])){ // update
				$query = "UPDATE " . $this->tables['patients'] . " SET 
						name = ?,
						lastname = ?,
						born_date = ?,
						gender = ?,
						medical_condition = ?,
						drugs = ?,
						sedentary = ?,
						smoking = ?,
						diabetes = ?
						WHERE id = ?";

				$udata = array(
					$data['name'],
					$data['lastname'],
					$data['born_date'],
					$data['gender'],
					$data['medical_condition'],
					$data['drugs'],
					$data['sedentary'],
					$data['smoking'],
					$data['diabetes'],
					$data['id']
					);

				$stmt = $this->db->prepare($query);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

				if($affected > 0 ){
					$this->response->setResponse(true);
					$this->response->result = $this->message->getSuccess(4);
				}else{
					$this->response->setResponse(false, $this->getError(4));
				}
			}else{ // insert
				// Verify if exist patient (only the active patients)
				if(!count($this->getByDocument($data['document'])->result) > 0){
					$query = "INSERT INTO " . $this->tables['patients'] . "(name, lastname, document, born_date, gender, medical_condition, drugs, sedentary, smoking, diabetes, doctor_id, device_id, remote_id, api_version) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?," . $this->api_version . ")";

					$idata = array(
						$data['name'],
						$data['lastname'],
						$data['document'],
						$data['born_date'],
						$data['gender'],
						$data['medical_condition'],
						$data['drugs'],
						$data['sedentary'],
						$data['smoking'],
						$data['diabetes'],
						$data['doctor_id'],
						$data['device_id'],
						$data['remote_id']
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
					$this->response->setResponse(false, $this->message->getError(7));
				}
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	//delete a patient
	public function delete($id){
		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tables['patients'] . " SET status = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			if($affected > 0 ){
				$this->response->setResponse(true, $this->message->getSuccess(6));
				$this->response->result = array("success" => "1");
			}else{
				$this->response->setResponse(false, $this->message->getError(6));
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}
}