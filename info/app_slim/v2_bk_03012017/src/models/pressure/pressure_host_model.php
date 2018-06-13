<?php

namespace App\v2\src\models\pressure;

use App\v2\src\libs\Response;

class Pressure_host_model extends Pressure_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAll(){
		try{
			$result = array();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['host'] . " WHERE state = 1");
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
			$stmt = $this->db->prepare("SELECT * FROM ". $this->tables['host'] . " WHERE id = $id AND state = 1");
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
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['host'] . " WHERE company_id = $company_id AND state = 1");
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

				$sql = "UPDATE " . $this->tables['host'] . " SET 
						type = ?,
						url = ?,
						db = ?,
						user = ?,
						pass = ?
						WHERE id = ?";

				$udata = array(
					$data['type'],
					$data['url'],
					$data['db'],
					$data['user'],
					$data['pass'],
					$data['id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$affected = $stmt->rowCount();

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['host'] . " (type, url, db, user, pass) VALUES (?,?,?,?,?)";
				$idata = array(
					$data['type'],
					$data['url'],
					$data['db'],
					$data['user'],
					$data['pass']
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
			$stmt = $this->db->prepare("UPDATE " . $this->tables['host'] . " SET state = 0 WHERE id = $id");
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