<?php

namespace App\v2\src\models\pressure;

use App\v2\src\libs\Response;

class Pressure_meassure_model extends Pressure_model{
	
	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function GetAll(){
		try{
			$result = array();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['meassure'] . " WHERE state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function Get($id){
		try{
			$result = array();

			// consult db
			$stmt = $this->db->prepare("SELECT * FROM ". $this->tables['meassure'] . " WHERE patient_id = $patient_id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}


	public function GetPatientMessures($patient_id){
		try{
			$result = array();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['meassure'] . " WHERE patient_id = $patient_id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function InsertOrUpdate($data){

		try{
			if(isset($data['id'])){ // Update

				$sql = "UPDATE " . $this->tables['meassure'] . " SET 
						patient_id = ?,
						data_ac = ?,
						data_dc = ?,
						pressure_avg = ?,
						pressure_sys = ?,
						pressure_dia = ?,
						meassure_time = ?,
						meassure_date = ?
						WHERE id = ?";

				$udata = array(
					$data['patient_id'],
					$data['data_ac'],
					$data['data_dc'],
					$data['avg'],
					$data['sys'],
					$data['dia'],
					$data['time'],
					$data['date'],
					$data['id']
				);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['meassure'] . " (patient_id, data_ac, data_dc, pressure_avg, pressure_sys, pressure_dia, meassure_time, meassure_date) VALUES (?,?,?,?,?,?,?,?)";

				$idata = array(
					$data['patient_id'],
					$data['data_ac'],
					$data['data_dc'],
					$data['avg'],
					$data['sys'],
					$data['dia'],
					$data['time'],
					$data['date']
				);


				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$affected = $stmt->rowCount();

			}

			$this->response->setResponse(true);
			$this->response->result = $affected;

			return $this->response;

		}catch(Exception $e){
			$this->response->setResponse(false);
			return $this->response;
		}
	}

	public function Delete($id){
		try{
			$result = array();

			// db consult
			$stmt = $this->db->prepare("UPDATE " . $this->tables['meassure'] . " SET state = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			$this->response->setResponse(true);
			$this->response->result = $affected;

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}
}
?>