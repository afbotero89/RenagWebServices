<?php

namespace App\v1\src\models\pressure;

use App\v1\src\libs\Response;

class Pressure_branch_model extends Pressure_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAll(){
		try{
			$result = array();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['branch'] . " WHERE state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function get($id){
		try{
			$result = array();

			// consult db
			$stmt = $this->db->prepare("SELECT * FROM ". $this->tables['branch'] . " WHERE id = $id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function getCompanyBranches($company_id){
		try{
			$result = array();

			//consult db
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['branch'] . " WHERE company_id = $company_id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function insertOrUpdate($data){

		try{
			if(isset($data['id'])){ // Update

				$sql = "UPDATE " . $this->tables['branch'] . " SET 
						company_id = ?,
						host_id = ?,
						name = ?,
						nit = ?,
						phone = ?
						WHERE id = ?";

				$udata = array(
					$data['company_id'],
					$data['host_id'],
					$data['name'],
					$data['nit'],
					$data['phone']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$affected = $stmt->rowCount();

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['branch'] . " (company_id, host_id, name, nit, phone) VALUES (?,?,?,?,?)";
				$idata = array(
					$data['company_id'],
					$data['host_id'],
					$data['name'],
					$data['nit'],
					$data['phone']
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

	public function delete($id){
		try{
			$result = array();

			// db consult
			$stmt = $this->db->prepare("UPDATE " . $this->tables['branch'] . " SET state = 0 WHERE id = $id");
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