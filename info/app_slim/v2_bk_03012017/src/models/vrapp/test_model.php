<?php

namespace App\v2\src\models\vrapp;

use App\v2\src\libs\Message;
use App\v2\src\libs\Response;

class Vrapp_test_model extends Vrapp_model{

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
			$stmt = $this->db->prepare($query . " status = 1 AND api_version = " .$this->api_version);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	//get all tests
	public function getAll(){
		$query = "SELECT * FROM " . $this->tables['tests'] . " WHERE";

		return $this->constructGet($query);
	}

	//get all tests by patient
	public function getAllByPatient($patient_id){
		$query = "SELECT * FROM " . $this->tables['tests'] . " WHERE patient_id = $patient_id AND";

		return $this->constructGet($query);
	}

	//get active by patient
	public function getActiveByPatient($patient_id){
		$query = "SELECT * FROM " . $this->tables['tests'] . " WHERE patient_id = $patient_id AND";

		return $this->constructGet($query);
	}

		// get patient by document
	public function getById($id){
		$query = "SELECT * FROM " . $this->tables['tests'] . " WHERE id = $id AND";

		return $this->constructGet($query);
	}

	// Insert or update a test
	public function insertOrUpdate($data){
		try{
			if(isset($data['id'])){ // update
				$query = "UPDATE " . $this->tables['tests'] . " SET 
						abdom_circ = ?,
						WHERE id = ?";

				$udata = array(
					$data['abdom_circ'],
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
				$query = "INSERT INTO " . $this->tables['tests'] . "(occlusion_time, recovery_time, vr_index, stop_pressure, sys_pressure, dia_pressure, avg_pressure, pulse_pressure, init_amp, min_amp, max_amp, final_amp, rising_time, date_test, abdom_circ, chart_data, final_state, patient_id, device_id, remote_id, api_version) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?," . $this->api_version . ")";

				$idata = array(
					$data['occlusion_time'],
					$data['recovery_time'],
					$data['vr_index'],
					$data['stop_pressure'],
					$data['sys_pressure'],
					$data['dia_pressure'],
					$data['avg_pressure'],
					$data['pulse_pressure'],
					$data['init_amp'],
					$data['min_amp'],
					$data['max_amp'],
					$data['final_amp'],
					$data['rising_time'],
					$data['date_test'],
					$data['abdom_circ'],
					$data['chart_data'],
					$data['final_state'],
					$data['patient_id'],
					$data['device_id'],
					$data['remote_id']
					);

				$stmt = $this->db->prepare($query);
				$stmt->execute($idata);
				$id = $this->db->lastInsertId();
				$affected = $stmt->rowCount();

				if($affected > 0 ){
					$this->response->setResponse(true);
					$this->response->result = $id;
				}else{
					$this->response->setResponse(false, $this->message->getError(5));
				}
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	//delete a test
	public function delete($id){
		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tables['tests'] . " SET status = 0 WHERE = id = $id");
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